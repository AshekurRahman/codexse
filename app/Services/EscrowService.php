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
use Illuminate\Support\Facades\Auth;
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
        // Use service commission rate from admin settings, fallback to config
        $this->platformFeeRate = CommissionSettings::getServiceCommissionRate()
            ?: (config('escrow.platform_fee_percent', 20) / 100);

        // Database setting takes priority, then config
        $configAutoRelease = config('escrow.auto_release_days', 3);
        $this->autoReleaseAfterDays = (int) Setting::get('escrow_auto_release_days', $configAutoRelease);

        // Set Stripe API key if configured
        if ($this->stripeService->isConfigured()) {
            Stripe::setApiKey($this->stripeService->getSecretKey());
        }
    }

    /**
     * Get the auto-release days setting.
     */
    public function getAutoReleaseDays(): int
    {
        return $this->autoReleaseAfterDays;
    }

    /**
     * Get the platform fee rate.
     */
    public function getPlatformFeeRate(): float
    {
        return $this->platformFeeRate;
    }

    /**
     * Check if escrow payments are properly configured.
     */
    public function isConfigured(): bool
    {
        return $this->stripeService->isConfigured();
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
        if (!$this->isConfigured()) {
            Log::warning('Escrow: Stripe not configured', [
                'payer_id' => $payer->id,
                'seller_id' => $seller->id,
                'amount' => $amount,
            ]);
            throw new \Exception('Payment gateway is not configured.');
        }

        // Validate seller has a user account
        if (!$seller->user) {
            Log::error('Escrow: Seller has no user account', [
                'seller_id' => $seller->id,
                'payer_id' => $payer->id,
            ]);
            throw new \Exception('Seller account is not properly configured.');
        }

        $platformFee = round($amount * $this->platformFeeRate, 2);
        $sellerAmount = round($amount - $platformFee, 2);

        Log::info('Escrow: Creating PaymentIntent', [
            'payer_id' => $payer->id,
            'seller_id' => $seller->id,
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'seller_amount' => $sellerAmount,
            'currency' => $this->stripeService->getCurrency(),
            'escrowable_type' => class_basename($escrowable),
            'escrowable_id' => $escrowable->id,
        ]);

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

            Log::info('Escrow: PaymentIntent created', [
                'transaction_id' => $escrowTransaction->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);

            // Log the escrow creation
            ActivityLogService::logEscrowCreated($escrowTransaction, $payer);

            return [
                'payment_intent' => $paymentIntent,
                'escrow_transaction' => $escrowTransaction,
                'client_secret' => $paymentIntent->client_secret,
            ];
        } catch (\Exception $e) {
            Log::error('Escrow: PaymentIntent creation failed', [
                'payer_id' => $payer->id,
                'seller_id' => $seller->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Confirm that payment has been authorized and hold funds in escrow.
     */
    public function holdFunds(EscrowTransaction $transaction): bool
    {
        Log::info('Escrow: Attempting to hold funds', [
            'transaction_id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number ?? null,
            'amount' => $transaction->amount,
        ]);

        try {
            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);

            if ($paymentIntent->status !== 'requires_capture') {
                Log::warning('Escrow: PaymentIntent not ready for capture', [
                    'transaction_id' => $transaction->id,
                    'payment_intent_status' => $paymentIntent->status,
                    'expected_status' => 'requires_capture',
                ]);
                return false;
            }

            $transaction->update([
                'status' => 'held',
                'held_at' => now(),
                'auto_release_at' => now()->addDays($this->autoReleaseAfterDays),
            ]);

            Log::info('Escrow: Funds held successfully', [
                'transaction_id' => $transaction->id,
                'auto_release_at' => now()->addDays($this->autoReleaseAfterDays)->toDateTimeString(),
            ]);

            // Log the escrow hold
            ActivityLogService::logEscrowHeld($transaction);

            return true;
        } catch (\Exception $e) {
            Log::error('Escrow: Hold funds failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Release funds to the seller (capture the payment and transfer).
     */
    public function releaseFunds(EscrowTransaction $transaction, string $notes = null): bool
    {
        if (!$transaction->canRelease()) {
            Log::warning('Escrow: Cannot release - invalid state', [
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number ?? null,
                'status' => $transaction->status,
            ]);
            return false;
        }

        // Null safety: Ensure seller exists
        $seller = $transaction->seller;
        if (!$seller) {
            Log::error('Escrow: Cannot release - seller not found', [
                'transaction_id' => $transaction->id,
                'seller_id' => $transaction->seller_id,
            ]);
            return false;
        }

        Log::info('Escrow: Attempting to release funds', [
            'transaction_id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number ?? null,
            'amount' => $transaction->amount,
            'seller_amount' => $transaction->seller_amount,
            'seller_id' => $seller->id,
        ]);

        try {
            DB::beginTransaction();

            // Capture the payment
            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);
            $paymentIntent->capture();

            // Transfer to seller's connected Stripe account (if available)
            $stripeTransferId = null;
            if ($seller->stripe_account_id && $seller->stripe_onboarding_complete) {
                $transfer = Transfer::create([
                    'amount' => (int) ($transaction->seller_amount * 100),
                    'currency' => $transaction->currency,
                    'destination' => $seller->stripe_account_id,
                    'transfer_group' => $transaction->transaction_number,
                    'metadata' => [
                        'escrow_transaction_id' => $transaction->id,
                    ],
                ]);
                $stripeTransferId = $transfer->id;

                Log::info('Escrow: Stripe Connect transfer created', [
                    'transaction_id' => $transaction->id,
                    'transfer_id' => $stripeTransferId,
                ]);
            } else {
                // If no Stripe Connect, add to seller's wallet
                $sellerUser = $seller->user;
                if (!$sellerUser) {
                    throw new \Exception('Seller user account not found');
                }

                $wallet = $sellerUser->getOrCreateWallet();
                $wallet->deposit(
                    amount: $transaction->seller_amount,
                    description: 'Escrow release: ' . ($transaction->transaction_number ?? $transaction->id),
                    paymentMethod: 'escrow',
                    paymentId: $transaction->transaction_number ?? (string) $transaction->id,
                    transactionable: $transaction
                );

                // Also update seller's total earnings for analytics
                $seller->increment('total_earnings', $transaction->seller_amount);

                Log::info('Escrow: Funds deposited to seller wallet', [
                    'transaction_id' => $transaction->id,
                    'seller_id' => $seller->id,
                    'wallet_id' => $wallet->id,
                    'amount' => $transaction->seller_amount,
                ]);
            }

            $transaction->update([
                'status' => 'released',
                'stripe_transfer_id' => $stripeTransferId,
                'released_at' => now(),
                'notes' => $notes,
            ]);

            DB::commit();

            Log::info('Escrow: Released successfully', [
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number ?? null,
                'seller_amount' => $transaction->seller_amount,
            ]);

            // Log the escrow release
            ActivityLogService::logEscrowReleased($transaction, Auth::user(), $notes);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Escrow: Release failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Refund funds to the payer.
     */
    public function refundFunds(EscrowTransaction $transaction, string $reason = null): bool
    {
        if (!$transaction->canRefund()) {
            Log::warning('Escrow: Cannot refund - invalid state', [
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number ?? null,
                'status' => $transaction->status,
            ]);
            return false;
        }

        Log::info('Escrow: Attempting refund', [
            'transaction_id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number ?? null,
            'amount' => $transaction->amount,
            'reason' => $reason,
        ]);

        try {
            DB::beginTransaction();

            $paymentIntent = PaymentIntent::retrieve($transaction->stripe_payment_intent_id);

            if ($paymentIntent->status === 'requires_capture') {
                // Cancel the authorization (no capture needed)
                $paymentIntent->cancel();
                Log::info('Escrow: Authorization cancelled', [
                    'transaction_id' => $transaction->id,
                ]);
            } elseif ($paymentIntent->status === 'succeeded') {
                // Already captured, need to refund
                \Stripe\Refund::create([
                    'payment_intent' => $transaction->stripe_payment_intent_id,
                ]);
                Log::info('Escrow: Refund created for captured payment', [
                    'transaction_id' => $transaction->id,
                ]);
            }

            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'notes' => $reason,
            ]);

            DB::commit();

            Log::info('Escrow: Refunded successfully', [
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number ?? null,
                'amount' => $transaction->amount,
            ]);

            // Log the escrow refund
            ActivityLogService::logEscrowRefunded($transaction, Auth::user(), $reason);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Escrow: Refund failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark escrow as disputed.
     */
    public function markAsDisputed(EscrowTransaction $transaction, ?User $disputedBy = null): bool
    {
        if (!$transaction->canDispute()) {
            return false;
        }

        $transaction->update([
            'status' => 'disputed',
        ]);

        // Log the escrow dispute
        if ($disputedBy) {
            ActivityLogService::logEscrowDisputed($transaction, $disputedBy);
        }

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
