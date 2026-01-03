<?php

namespace App\Http\Controllers;

use App\Models\EscrowTransaction;
use App\Models\JobMilestone;
use App\Models\ServiceOrder;
use App\Services\EscrowService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class EscrowController extends Controller
{
    protected EscrowService $escrowService;
    protected StripeService $stripeService;

    public function __construct(EscrowService $escrowService, StripeService $stripeService)
    {
        $this->escrowService = $escrowService;
        $this->stripeService = $stripeService;
    }

    /**
     * Show checkout page for a service order.
     */
    public function checkoutServiceOrder(ServiceOrder $serviceOrder)
    {
        // Check if payment gateway is configured
        if (!$this->escrowService->isConfigured()) {
            Log::warning('Escrow checkout: Stripe not configured', [
                'user_id' => auth()->id(),
                'service_order_id' => $serviceOrder->id,
            ]);
            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('error', 'Payment gateway is not configured. Please contact support.');
        }

        // Verify the user is the buyer
        if ($serviceOrder->buyer_id !== auth()->id()) {
            abort(403, 'You are not authorized to pay for this order.');
        }

        if ($serviceOrder->status !== 'pending_payment') {
            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('error', 'This order cannot be paid for.');
        }

        // Verify seller exists
        if (!$serviceOrder->seller) {
            Log::error('Escrow checkout: Seller not found', [
                'service_order_id' => $serviceOrder->id,
                'seller_id' => $serviceOrder->seller_id,
            ]);
            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('error', 'Seller account not found. Please contact support.');
        }

        $fees = $this->escrowService->calculateFees($serviceOrder->price);

        Log::info('Escrow checkout: Showing service order checkout', [
            'user_id' => auth()->id(),
            'service_order_id' => $serviceOrder->id,
            'amount' => $serviceOrder->price,
        ]);

        return view('pages.escrow.checkout', [
            'escrowable' => $serviceOrder,
            'type' => 'service_order',
            'fees' => $fees,
            'stripePublicKey' => $this->escrowService->getPublicKey(),
        ]);
    }

    /**
     * Show checkout page for a job milestone.
     */
    public function checkoutMilestone(JobMilestone $milestone)
    {
        $contract = $milestone->contract;

        // Check if payment gateway is configured
        if (!$this->escrowService->isConfigured()) {
            Log::warning('Escrow checkout: Stripe not configured', [
                'user_id' => auth()->id(),
                'milestone_id' => $milestone->id,
            ]);
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Payment gateway is not configured. Please contact support.');
        }

        // Verify the user is the client
        if ($contract->client_id !== auth()->id()) {
            abort(403, 'You are not authorized to fund this milestone.');
        }

        if (!$milestone->canFund()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This milestone cannot be funded.');
        }

        // Verify seller exists
        if (!$contract->seller) {
            Log::error('Escrow checkout: Seller not found', [
                'milestone_id' => $milestone->id,
                'contract_id' => $contract->id,
                'seller_id' => $contract->seller_id,
            ]);
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Seller account not found. Please contact support.');
        }

        $fees = $this->escrowService->calculateFees($milestone->amount, 'job');

        Log::info('Escrow checkout: Showing milestone checkout', [
            'user_id' => auth()->id(),
            'milestone_id' => $milestone->id,
            'contract_id' => $contract->id,
            'amount' => $milestone->amount,
        ]);

        return view('pages.escrow.checkout', [
            'escrowable' => $milestone,
            'type' => 'milestone',
            'contract' => $contract,
            'fees' => $fees,
            'stripePublicKey' => $this->escrowService->getPublicKey(),
        ]);
    }

    /**
     * Create PaymentIntent for escrow payment.
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'type' => 'required|in:service_order,milestone',
            'id' => 'required|integer',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');

        // Check if payment gateway is configured
        if (!$this->escrowService->isConfigured()) {
            Log::warning('Escrow: PaymentIntent creation failed - Stripe not configured', [
                'user_id' => auth()->id(),
                'type' => $type,
                'id' => $id,
            ]);
            return response()->json(['error' => 'Payment gateway is not configured.'], 503);
        }

        Log::info('Escrow: Creating PaymentIntent', [
            'user_id' => auth()->id(),
            'type' => $type,
            'id' => $id,
        ]);

        try {
            if ($type === 'service_order') {
                $escrowable = ServiceOrder::findOrFail($id);

                if ($escrowable->buyer_id !== auth()->id()) {
                    Log::warning('Escrow: Unauthorized PaymentIntent attempt', [
                        'user_id' => auth()->id(),
                        'service_order_id' => $id,
                        'buyer_id' => $escrowable->buyer_id,
                    ]);
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $seller = $escrowable->seller;
                if (!$seller) {
                    Log::error('Escrow: Seller not found for service order', [
                        'service_order_id' => $id,
                        'seller_id' => $escrowable->seller_id,
                    ]);
                    return response()->json(['error' => 'Seller not found.'], 404);
                }

                $amount = $escrowable->price;
                $description = "Service Order: {$escrowable->order_number}";
            } else {
                $escrowable = JobMilestone::findOrFail($id);
                $contract = $escrowable->contract;

                if ($contract->client_id !== auth()->id()) {
                    Log::warning('Escrow: Unauthorized milestone PaymentIntent attempt', [
                        'user_id' => auth()->id(),
                        'milestone_id' => $id,
                        'client_id' => $contract->client_id,
                    ]);
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $seller = $contract->seller;
                if (!$seller) {
                    Log::error('Escrow: Seller not found for contract', [
                        'milestone_id' => $id,
                        'contract_id' => $contract->id,
                        'seller_id' => $contract->seller_id,
                    ]);
                    return response()->json(['error' => 'Seller not found.'], 404);
                }

                $amount = $escrowable->amount;
                $description = "Milestone: {$escrowable->title} ({$contract->contract_number})";
            }

            $result = $this->escrowService->createPaymentIntent(
                $escrowable,
                auth()->user(),
                $seller,
                $amount,
                $description
            );

            Log::info('Escrow: PaymentIntent created successfully', [
                'user_id' => auth()->id(),
                'type' => $type,
                'id' => $id,
                'transaction_id' => $result['escrow_transaction']->id,
            ]);

            return response()->json([
                'clientSecret' => $result['client_secret'],
                'transactionId' => $result['escrow_transaction']->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Escrow: Create PaymentIntent failed', [
                'user_id' => auth()->id(),
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to create payment. Please try again.'], 500);
        }
    }

    /**
     * Handle successful payment confirmation.
     * Note: This can be called after user returns from Stripe, potentially with expired session.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
        ]);

        $transactionId = $request->input('transaction_id');
        $transaction = EscrowTransaction::find($transactionId);

        if (!$transaction) {
            Log::error('Escrow: Transaction not found for confirmation', [
                'transaction_id' => $transactionId,
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Transaction not found. Please contact support.');
        }

        // Verify ownership (if user is authenticated)
        if (auth()->check() && $transaction->payer_id !== auth()->id()) {
            Log::warning('Escrow: Unauthorized confirmation attempt', [
                'transaction_id' => $transactionId,
                'user_id' => auth()->id(),
                'payer_id' => $transaction->payer_id,
            ]);
            abort(403, 'You are not authorized to confirm this payment.');
        }

        Log::info('Escrow: Confirming payment', [
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'amount' => $transaction->amount,
        ]);

        // Hold the funds
        if ($this->escrowService->holdFunds($transaction)) {
            // Update the escrowable status
            $escrowable = $transaction->escrowable;

            if ($escrowable instanceof ServiceOrder) {
                $newStatus = $escrowable->service?->requirements()->exists()
                    ? 'pending_requirements'
                    : 'ordered';

                $escrowable->update(['status' => $newStatus]);

                Log::info('Escrow: Service order payment confirmed', [
                    'transaction_id' => $transaction->id,
                    'service_order_id' => $escrowable->id,
                    'new_status' => $newStatus,
                ]);

                return redirect()->route('service-orders.show', $escrowable)
                    ->with('success', 'Payment successful! Your order has been placed.');

            } elseif ($escrowable instanceof JobMilestone) {
                $escrowable->update([
                    'status' => 'funded',
                    'funded_at' => now(),
                ]);

                // If this is the first funded milestone, activate the contract
                $contract = $escrowable->contract;
                if ($contract && $contract->status === 'pending') {
                    $contract->update([
                        'status' => 'active',
                        'started_at' => now(),
                    ]);
                }

                Log::info('Escrow: Milestone funded successfully', [
                    'transaction_id' => $transaction->id,
                    'milestone_id' => $escrowable->id,
                    'contract_id' => $contract?->id,
                ]);

                return redirect()->route('contracts.show', $contract)
                    ->with('success', 'Milestone funded successfully!');
            }
        }

        Log::error('Escrow: Payment confirmation failed', [
            'transaction_id' => $transaction->id,
        ]);

        return redirect()->back()->with('error', 'Failed to process payment. Please contact support.');
    }

    /**
     * Handle cancelled payment.
     */
    public function cancelPayment(Request $request)
    {
        Log::info('Escrow: Payment cancelled by user', [
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->with('error', 'Payment was cancelled.');
    }

    /**
     * Webhook handler for escrow-related Stripe events.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = $this->stripeService->getWebhookSecret();

        if (!$webhookSecret) {
            Log::error('Escrow webhook: Webhook secret not configured');
            return response('Webhook secret not configured', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Escrow webhook: Signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response('Webhook error', 400);
        }

        Log::info('Escrow webhook: Received event', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentSucceeded($paymentIntent->id);
                Log::info('Escrow webhook: Payment succeeded', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentFailed($paymentIntent->id);
                Log::warning('Escrow webhook: Payment failed', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                break;

            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentFailed($paymentIntent->id);
                Log::info('Escrow webhook: Payment canceled', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                break;

            default:
                Log::info('Escrow webhook: Unhandled event type', [
                    'type' => $event->type,
                ]);
        }

        return response('OK', 200);
    }

    /**
     * Show escrow transaction details.
     */
    public function show(EscrowTransaction $transaction)
    {
        // Verify the user is payer or payee
        if ($transaction->payer_id !== auth()->id() && $transaction->payee_id !== auth()->id()) {
            abort(403);
        }

        return view('pages.escrow.show', compact('transaction'));
    }

    /**
     * Buyer approves work and releases escrow.
     */
    public function release(Request $request, EscrowTransaction $transaction)
    {
        // Only payer can release
        if ($transaction->payer_id !== auth()->id()) {
            Log::warning('Escrow: Unauthorized release attempt', [
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'payer_id' => $transaction->payer_id,
            ]);
            abort(403, 'You are not authorized to release this escrow.');
        }

        if (!$transaction->canRelease()) {
            Log::warning('Escrow: Cannot release - invalid state', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]);
            return redirect()->back()->with('error', 'This escrow cannot be released.');
        }

        Log::info('Escrow: Releasing funds manually', [
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'amount' => $transaction->seller_amount,
        ]);

        if ($this->escrowService->releaseFunds($transaction, 'Released by buyer')) {
            return redirect()->back()->with('success', 'Payment has been released to the seller.');
        }

        return redirect()->back()->with('error', 'Failed to release payment. Please contact support.');
    }

    /**
     * Request refund (initiates dispute if seller doesn't agree).
     */
    public function requestRefund(Request $request, EscrowTransaction $transaction)
    {
        // Only payer can request refund
        if ($transaction->payer_id !== auth()->id()) {
            Log::warning('Escrow: Unauthorized refund request', [
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'payer_id' => $transaction->payer_id,
            ]);
            abort(403, 'You are not authorized to request a refund for this escrow.');
        }

        if (!$transaction->canRefund()) {
            Log::warning('Escrow: Cannot refund - invalid state', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]);
            return redirect()->back()->with('error', 'This escrow cannot be refunded.');
        }

        Log::info('Escrow: Refund requested', [
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
        ]);

        // This will trigger a dispute process
        return redirect()->route('disputes.create', [
            'transaction' => $transaction->id,
        ])->with('info', 'Please provide details for your refund request.');
    }
}
