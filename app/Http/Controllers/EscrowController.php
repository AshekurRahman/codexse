<?php

namespace App\Http\Controllers;

use App\Models\EscrowTransaction;
use App\Models\JobMilestone;
use App\Models\ServiceOrder;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class EscrowController extends Controller
{
    protected EscrowService $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }

    /**
     * Show checkout page for a service order.
     */
    public function checkoutServiceOrder(ServiceOrder $serviceOrder)
    {
        // Verify the user is the buyer
        if ($serviceOrder->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($serviceOrder->status !== 'pending_payment') {
            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('error', 'This order cannot be paid for.');
        }

        $fees = $this->escrowService->calculateFees($serviceOrder->price);

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

        // Verify the user is the client
        if ($contract->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$milestone->canFund()) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'This milestone cannot be funded.');
        }

        $fees = $this->escrowService->calculateFees($milestone->amount, 'job');

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

        try {
            if ($type === 'service_order') {
                $escrowable = ServiceOrder::findOrFail($id);

                if ($escrowable->buyer_id !== auth()->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $seller = $escrowable->seller;
                $amount = $escrowable->price;
                $description = "Service Order: {$escrowable->order_number}";
            } else {
                $escrowable = JobMilestone::findOrFail($id);
                $contract = $escrowable->contract;

                if ($contract->client_id !== auth()->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $seller = $contract->seller;
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

            return response()->json([
                'clientSecret' => $result['client_secret'],
                'transactionId' => $result['escrow_transaction']->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Create PaymentIntent failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create payment. Please try again.'], 500);
        }
    }

    /**
     * Handle successful payment confirmation.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
        ]);

        $transaction = EscrowTransaction::findOrFail($request->input('transaction_id'));

        // Verify ownership
        if ($transaction->payer_id !== auth()->id()) {
            abort(403);
        }

        // Hold the funds
        if ($this->escrowService->holdFunds($transaction)) {
            // Update the escrowable status
            $escrowable = $transaction->escrowable;

            if ($escrowable instanceof ServiceOrder) {
                $escrowable->update([
                    'status' => $escrowable->service->requirements()->exists()
                        ? 'pending_requirements'
                        : 'ordered',
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
                if ($contract->status === 'pending') {
                    $contract->update([
                        'status' => 'active',
                        'started_at' => now(),
                    ]);
                }

                return redirect()->route('contracts.show', $contract)
                    ->with('success', 'Milestone funded successfully!');
            }
        }

        return redirect()->back()->with('error', 'Failed to process payment. Please contact support.');
    }

    /**
     * Handle cancelled payment.
     */
    public function cancelPayment(Request $request)
    {
        return redirect()->back()->with('error', 'Payment was cancelled.');
    }

    /**
     * Webhook handler for escrow-related Stripe events.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Escrow webhook error: ' . $e->getMessage());
            return response('Webhook error', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentSucceeded($paymentIntent->id);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentFailed($paymentIntent->id);
                break;

            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                $this->escrowService->handlePaymentFailed($paymentIntent->id);
                break;

            default:
                Log::info('Unhandled escrow webhook event: ' . $event->type);
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
            abort(403);
        }

        if (!$transaction->canRelease()) {
            return redirect()->back()->with('error', 'This escrow cannot be released.');
        }

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
            abort(403);
        }

        if (!$transaction->canRefund()) {
            return redirect()->back()->with('error', 'This escrow cannot be refunded.');
        }

        // This will trigger a dispute process
        return redirect()->route('disputes.create', [
            'transaction' => $transaction->id,
        ])->with('info', 'Please provide details for your refund request.');
    }
}
