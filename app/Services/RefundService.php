<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Refund;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;

class RefundService
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createRefundRequest(Order $order, float $amount, string $reason, string $type = 'full'): Refund
    {
        return Refund::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $amount,
            'type' => $type,
            'reason' => $reason,
            'payment_method' => $order->payment_method,
            'status' => 'pending',
        ]);
    }

    public function approveRefund(Refund $refund, int $adminId, ?string $notes = null): bool
    {
        $refund->update([
            'status' => 'approved',
            'processed_by' => $adminId,
            'admin_notes' => $notes,
            'processed_at' => now(),
        ]);

        return true;
    }

    public function rejectRefund(Refund $refund, int $adminId, string $notes): bool
    {
        $refund->update([
            'status' => 'rejected',
            'processed_by' => $adminId,
            'admin_notes' => $notes,
            'processed_at' => now(),
        ]);

        return true;
    }

    public function processRefund(Refund $refund): bool
    {
        if (!$refund->canProcess()) {
            return false;
        }

        $refund->update(['status' => 'processing']);

        try {
            DB::beginTransaction();

            $order = $refund->order;
            $success = false;

            switch ($order->payment_method) {
                case 'stripe':
                    $success = $this->processStripeRefund($refund, $order);
                    break;
                case 'paypal':
                    $success = $this->processPayPalRefund($refund, $order);
                    break;
                case 'payoneer':
                    $success = $this->processPayoneerRefund($refund, $order);
                    break;
                case 'wallet':
                    $success = $this->processWalletRefund($refund, $order);
                    break;
                default:
                    throw new \Exception("Unsupported payment method: {$order->payment_method}");
            }

            if ($success) {
                $refund->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Update order status
                if ($refund->type === 'full') {
                    $order->update(['status' => 'refunded']);
                } else {
                    // For partial refunds, keep track
                    $totalRefunded = Refund::where('order_id', $order->id)
                        ->where('status', 'completed')
                        ->sum('amount');

                    if ($totalRefunded >= $order->total) {
                        $order->update(['status' => 'refunded']);
                    }
                }

                // Reverse seller earnings if applicable
                $this->reverseSellerEarnings($order, $refund->amount);

                DB::commit();
                return true;
            }

            throw new \Exception('Refund processing failed');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Refund processing failed', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage(),
            ]);

            $refund->update([
                'status' => 'failed',
                'admin_notes' => ($refund->admin_notes ?? '') . "\nError: " . $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function processStripeRefund(Refund $refund, Order $order): bool
    {
        if (!$order->stripe_payment_intent_id && !$order->charge_id) {
            throw new \Exception('No Stripe payment reference found');
        }

        Stripe::setApiKey($this->stripeService->getSecretKey());

        $refundParams = [
            'amount' => (int) ($refund->amount * 100), // Convert to cents
        ];

        if ($order->stripe_payment_intent_id) {
            $refundParams['payment_intent'] = $order->stripe_payment_intent_id;
        } elseif ($order->charge_id) {
            $refundParams['charge'] = $order->charge_id;
        }

        $stripeRefund = StripeRefund::create($refundParams);

        $refund->update(['stripe_refund_id' => $stripeRefund->id]);

        return $stripeRefund->status === 'succeeded';
    }

    protected function processPayPalRefund(Refund $refund, Order $order): bool
    {
        if (!$order->paypal_order_id) {
            throw new \Exception('No PayPal order reference found');
        }

        // PayPal refund implementation
        // This would integrate with PayPal's refund API
        // For now, mark as manual processing required
        Log::info('PayPal refund requires manual processing', [
            'refund_id' => $refund->id,
            'paypal_order_id' => $order->paypal_order_id,
        ]);

        // In production, you would call PayPal's refund API here
        // $refund->update(['paypal_refund_id' => $paypalRefundId]);

        return true;
    }

    protected function processPayoneerRefund(Refund $refund, Order $order): bool
    {
        if (!$order->payoneer_transaction_id) {
            throw new \Exception('No Payoneer transaction reference found');
        }

        // Payoneer refund implementation
        Log::info('Payoneer refund requires manual processing', [
            'refund_id' => $refund->id,
            'payoneer_transaction_id' => $order->payoneer_transaction_id,
        ]);

        return true;
    }

    protected function processWalletRefund(Refund $refund, Order $order): bool
    {
        $user = $order->user;
        $wallet = $user->getOrCreateWallet();

        // Credit the refund amount back to wallet
        $wallet->credit(
            $refund->amount,
            'refund',
            "Refund for order {$order->order_number}",
            $order
        );

        return true;
    }

    protected function reverseSellerEarnings(Order $order, float $refundAmount): void
    {
        // Calculate the proportion of the refund
        $refundProportion = $refundAmount / $order->total;

        foreach ($order->items as $item) {
            if (!$item->seller_id) continue;

            $seller = $item->seller;
            if (!$seller) continue;

            $sellerWallet = $seller->user->wallet;
            if (!$sellerWallet) continue;

            // Calculate seller's share of the refund
            $sellerRefundAmount = $item->seller_amount * $refundProportion;

            if ($sellerRefundAmount > 0 && $sellerWallet->balance >= $sellerRefundAmount) {
                $sellerWallet->debit(
                    $sellerRefundAmount,
                    'refund',
                    "Refund deduction for order {$order->order_number}",
                    $order
                );
            }
        }
    }

    public function getRefundableAmount(Order $order): float
    {
        $completedRefunds = Refund::where('order_id', $order->id)
            ->where('status', 'completed')
            ->sum('amount');

        return max(0, $order->total - $completedRefunds);
    }

    public function canRefund(Order $order): bool
    {
        if (!in_array($order->status, ['completed', 'processing'])) {
            return false;
        }

        return $this->getRefundableAmount($order) > 0;
    }
}
