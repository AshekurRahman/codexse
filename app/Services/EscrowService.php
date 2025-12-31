<?php

namespace App\Services;

use App\Filament\Admin\Pages\CommissionSettings;
use App\Models\EscrowTransaction;
use App\Models\JobMilestone;
use App\Models\Seller;
use App\Models\ServiceOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Transfer;

class EscrowService
{
    protected StripeService $stripeService;
    protected float $platformFeeRate;
    protected int $autoReleaseAfterDays;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        // Use service commission rate from admin settings (default 20%)
        $this->platformFeeRate = CommissionSettings::getServiceCommissionRate();
        $this->autoReleaseAfterDays = (int) Setting::get('escrow_auto_release_days', 3);
    }

    /**
     * Create a PaymentIntent for escrow (manual capture).
     */
    public function createPaymentIntent(
        Model $escrowable,
        User $payer,
        Seller $seller,
        float $amount,
        string $description = null
    ): array {
        if (!$this->stripeService->isConfigured()) {
            throw new \Exception('Stripe is not configured.');
        }

        $platformFee = round($amount * $this->platformFeeRate, 2);
        $sellerAmount = round($amount - $platformFee, 2);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($amount * 100), // Convert to cents
                'currency' => $this->stripeService->getCurrency(),
                'capture_method' => 'manual', // Authorize but don't capture yet
                'description' => $description ?? 'Escrow payment for ' . class_basename($escrowable),
                'metadata' => [
                    'escrowable_type' => get_class($escrowable),
                    'escrowable_id' => $escrowable->id,
                    'payer_id' => $payer->id,
                    'seller_id' => $seller->id,
                ],
            ]);

            // Create escrow transaction record
            $escrowTransaction = EscrowTransaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $seller->user_id,
                'seller_id' => $seller->id,
                'escrowable_type' => get_class($escrowable),
                'escrowable_id' => $escrowable->id,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'seller_amount' => $sellerAmount,
                'currency' => $this->stripeService->getCurrency(),
                'status' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            return [
                'payment_intent' => $paymentIntent,
                'escrow_transaction' => $escrowTransaction,
                'client_secret' => $paymentIntent->client_secret,
            ];
        } catch (\Exception $e) {
            Log::error('Escrow PaymentIntent creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirm that payment has been authorized and hold funds in escrow.
     */
    public function holdFunds(EscrowTransaction $transaction): bool
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);

            if ($paymentIntent->status !== 'requires_capture') {
                Log::warning('PaymentIntent not ready for capture: ' . $paymentIntent->status);
                return false;
            }

            $transaction->update([
                'status' => 'held',
                'held_at' => now(),
                'auto_release_at' => now()->addDays($this->autoReleaseAfterDays),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Escrow hold funds failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Release funds to the seller (capture the payment and transfer).
     */
    public function releaseFunds(EscrowTransaction $transaction, string $notes = null): bool
    {
        if (!$transaction->canRelease()) {
            Log::warning('Cannot release escrow: ' . $transaction->transaction_number);
            return false;
        }

        try {
            DB::beginTransaction();

            // Capture the payment
            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);
            $paymentIntent->capture();

            // Transfer to seller's connected Stripe account (if available)
            $stripeTransferId = null;
            if ($transaction->seller->stripe_account_id && $transaction->seller->stripe_onboarding_complete) {
                $transfer = Transfer::create([
                    'amount' => (int) ($transaction->seller_amount * 100),
                    'currency' => $transaction->currency,
                    'destination' => $transaction->seller->stripe_account_id,
                    'transfer_group' => $transaction->transaction_number,
                    'metadata' => [
                        'escrow_transaction_id' => $transaction->id,
                    ],
                ]);
                $stripeTransferId = $transfer->id;
            } else {
                // If no Stripe Connect, add to seller's platform balance
                $transaction->seller->increment('available_balance', $transaction->seller_amount);
                $transaction->seller->increment('total_earnings', $transaction->seller_amount);
            }

            $transaction->update([
                'status' => 'released',
                'stripe_transfer_id' => $stripeTransferId,
                'released_at' => now(),
                'notes' => $notes,
            ]);

            DB::commit();

            Log::info('Escrow released: ' . $transaction->transaction_number);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Escrow release failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refund funds to the payer.
     */
    public function refundFunds(EscrowTransaction $transaction, string $reason = null): bool
    {
        if (!$transaction->canRefund()) {
            Log::warning('Cannot refund escrow: ' . $transaction->transaction_number);
            return false;
        }

        try {
            DB::beginTransaction();

            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);

            if ($paymentIntent->status === 'requires_capture') {
                // Cancel the authorization (no capture needed)
                $paymentIntent->cancel();
            } elseif ($paymentIntent->status === 'succeeded') {
                // Already captured, need to refund
                \Stripe\Refund::create([
                    'payment_intent' => $transaction->stripe_payment_intent_id,
                ]);
            }

            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'notes' => $reason,
            ]);

            DB::commit();

            Log::info('Escrow refunded: ' . $transaction->transaction_number);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Escrow refund failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark escrow as disputed.
     */
    public function markAsDisputed(EscrowTransaction $transaction): bool
    {
        if (!$transaction->canDispute()) {
            return false;
        }

        $transaction->update([
            'status' => 'disputed',
        ]);

        return true;
    }

    /**
     * Process auto-release for eligible transactions.
     */
    public function processAutoRelease(): int
    {
        $transactions = EscrowTransaction::readyForAutoRelease()->get();
        $released = 0;

        foreach ($transactions as $transaction) {
            if ($this->releaseFunds($transaction, 'Auto-released after ' . $this->autoReleaseAfterDays . ' days')) {
                $released++;
            }
        }

        Log::info("Auto-released {$released} escrow transactions");
        return $released;
    }

    /**
     * Calculate platform fee for an amount.
     * @param float $amount The total amount
     * @param string $type The type of transaction: 'service', 'job', or 'product'
     */
    public function calculateFees(float $amount, string $type = 'service'): array
    {
        $feeRate = match ($type) {
            'job' => CommissionSettings::getJobCommissionRate(),
            'service' => CommissionSettings::getServiceCommissionRate(),
            default => $this->platformFeeRate,
        };

        $platformFee = round($amount * $feeRate, 2);
        $sellerAmount = round($amount - $platformFee, 2);

        return [
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'seller_amount' => $sellerAmount,
            'fee_rate' => $feeRate,
        ];
    }

    /**
     * Get the public key for frontend payment forms.
     */
    public function getPublicKey(): ?string
    {
        return $this->stripeService->getPublicKey();
    }

    /**
     * Handle successful payment webhook.
     */
    public function handlePaymentSucceeded(string $paymentIntentId): bool
    {
        $transaction = EscrowTransaction::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if (!$transaction) {
            Log::warning('No escrow transaction found for PaymentIntent: ' . $paymentIntentId);
            return false;
        }

        return $this->holdFunds($transaction);
    }

    /**
     * Handle payment failure webhook.
     */
    public function handlePaymentFailed(string $paymentIntentId): bool
    {
        $transaction = EscrowTransaction::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if (!$transaction) {
            return false;
        }

        $transaction->update([
            'status' => 'cancelled',
            'notes' => 'Payment failed',
        ]);

        return true;
    }
}
