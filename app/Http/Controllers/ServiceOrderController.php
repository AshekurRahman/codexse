<?php

namespace App\Http\Controllers;

use App\Models\ServiceDelivery;
use App\Models\ServiceOrder;
use App\Rules\SecureFileUpload;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceOrderController extends Controller
{
    /**
     * List buyer's service orders.
     */
    public function index(Request $request)
    {
        $query = ServiceOrder::where('buyer_id', auth()->id())
            ->with(['service', 'seller', 'package']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('pages.service-orders.index', compact('orders'));
    }

    /**
     * Show a single order.
     */
    public function show(ServiceOrder $serviceOrder)
    {
        // Only buyer or seller can view
        if ($serviceOrder->buyer_id !== auth()->id() &&
            (!auth()->user()->seller || auth()->user()->seller->id !== $serviceOrder->seller_id)) {
            abort(403);
        }

        $serviceOrder->load([
            'service',
            'seller',
            'package',
            'deliveries' => function ($q) {
                $q->latest();
            },
            'conversation.messages' => function ($q) {
                $q->with('sender')->latest()->limit(20);
            },
            'escrowTransaction'
        ]);

        return view('pages.service-orders.show', compact('serviceOrder'));
    }

    /**
     * Submit requirements for an order.
     */
    public function submitRequirements(Request $request, ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($serviceOrder->status !== 'pending_requirements') {
            return redirect()->back()->with('error', 'Requirements cannot be submitted for this order.');
        }

        // Validate and process requirements
        $rules = [];
        $requirementsData = $serviceOrder->requirements_data ?? [];

        foreach ($serviceOrder->service->requirements as $requirement) {
            $key = "requirements.{$requirement->id}";

            if ($requirement->is_required && empty($requirementsData[$requirement->id]['answer'] ?? null)) {
                $rules[$key] = 'required';
            } else {
                $rules[$key] = 'nullable';
            }

            if ($requirement->type === 'file') {
                $rules[$key] = [$rules[$key], 'file', SecureFileUpload::attachment(10)];
            }
        }

        $validated = $request->validate($rules);

        // Process requirements data
        foreach ($serviceOrder->service->requirements as $requirement) {
            $value = $request->input("requirements.{$requirement->id}");

            if ($requirement->type === 'file' && $request->hasFile("requirements.{$requirement->id}")) {
                $file = $request->file("requirements.{$requirement->id}");
                $path = $file->store('service-requirements/' . $serviceOrder->service_id, 'public');
                $value = $path;
            }

            if ($value !== null) {
                $requirementsData[$requirement->id] = [
                    'question' => $requirement->question,
                    'answer' => $value,
                    'type' => $requirement->type,
                ];
            }
        }

        $serviceOrder->update([
            'requirements_data' => $requirementsData,
            'status' => 'ordered',
        ]);

        return redirect()->route('service-orders.show', $serviceOrder)
            ->with('success', 'Requirements submitted successfully. The seller will start working on your order.');
    }

    /**
     * Approve a delivery.
     */
    public function approveDelivery(Request $request, ServiceOrder $serviceOrder, EscrowService $escrowService)
    {
        if ($serviceOrder->buyer_id !== auth()->id()) {
            Log::warning('ServiceOrder: Unauthorized approval attempt', [
                'service_order_id' => $serviceOrder->id,
                'user_id' => auth()->id(),
                'buyer_id' => $serviceOrder->buyer_id,
            ]);
            abort(403, 'You are not authorized to approve this order.');
        }

        if (!$serviceOrder->canApprove()) {
            Log::warning('ServiceOrder: Cannot approve - invalid state', [
                'service_order_id' => $serviceOrder->id,
                'status' => $serviceOrder->status,
            ]);
            return redirect()->back()->with('error', 'This order cannot be approved.');
        }

        Log::info('ServiceOrder: Approving delivery', [
            'service_order_id' => $serviceOrder->id,
            'user_id' => auth()->id(),
            'order_number' => $serviceOrder->order_number ?? null,
        ]);

        try {
            DB::beginTransaction();

            // Update delivery status
            $latestDelivery = $serviceOrder->latestDelivery;
            if ($latestDelivery) {
                $latestDelivery->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                ]);
            }

            // Complete the order
            $serviceOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_notes' => $request->input('notes'),
            ]);

            // Release escrow funds
            if ($serviceOrder->escrowTransaction) {
                $released = $escrowService->releaseFunds($serviceOrder->escrowTransaction, 'Buyer approved delivery');
                if (!$released) {
                    Log::warning('ServiceOrder: Escrow release failed but order completed', [
                        'service_order_id' => $serviceOrder->id,
                        'transaction_id' => $serviceOrder->escrowTransaction->id,
                    ]);
                }
            }

            DB::commit();

            Log::info('ServiceOrder: Delivery approved successfully', [
                'service_order_id' => $serviceOrder->id,
                'seller_id' => $serviceOrder->seller_id,
            ]);

            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('success', 'Order completed! Payment has been released to the seller.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ServiceOrder: Failed to approve delivery', [
                'service_order_id' => $serviceOrder->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to complete order. Please try again.');
        }
    }

    /**
     * Request a revision.
     */
    public function requestRevision(Request $request, ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (!$serviceOrder->canRequestRevision()) {
            return redirect()->back()->with('error', 'Revision cannot be requested for this order.');
        }

        $request->validate([
            'revision_notes' => 'required|string|max:2000',
        ]);

        DB::beginTransaction();

        try {
            // Update delivery status
            $latestDelivery = $serviceOrder->latestDelivery;
            if ($latestDelivery) {
                $latestDelivery->update([
                    'status' => 'revision_requested',
                    'revision_notes' => $request->input('revision_notes'),
                    'responded_at' => now(),
                ]);
            }

            // Update order
            $serviceOrder->update([
                'status' => 'revision_requested',
            ]);
            $serviceOrder->increment('revisions_used');

            DB::commit();

            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('success', 'Revision requested. The seller will be notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to request revision. Please try again.');
        }
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, ServiceOrder $serviceOrder, EscrowService $escrowService)
    {
        if ($serviceOrder->buyer_id !== auth()->id()) {
            Log::warning('ServiceOrder: Unauthorized cancel attempt', [
                'service_order_id' => $serviceOrder->id,
                'user_id' => auth()->id(),
                'buyer_id' => $serviceOrder->buyer_id,
            ]);
            abort(403, 'You are not authorized to cancel this order.');
        }

        if (!$serviceOrder->canCancel()) {
            Log::warning('ServiceOrder: Cannot cancel - invalid state', [
                'service_order_id' => $serviceOrder->id,
                'status' => $serviceOrder->status,
            ]);
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        Log::info('ServiceOrder: Cancelling order', [
            'service_order_id' => $serviceOrder->id,
            'user_id' => auth()->id(),
            'reason' => $request->input('cancellation_reason'),
        ]);

        try {
            DB::beginTransaction();

            $serviceOrder->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('cancellation_reason'),
            ]);

            // Refund escrow if exists
            if ($serviceOrder->escrowTransaction) {
                $refunded = $escrowService->refundFunds($serviceOrder->escrowTransaction, 'Order cancelled by buyer');
                if ($refunded) {
                    Log::info('ServiceOrder: Escrow refunded successfully', [
                        'service_order_id' => $serviceOrder->id,
                        'transaction_id' => $serviceOrder->escrowTransaction->id,
                    ]);
                } else {
                    Log::warning('ServiceOrder: Escrow refund failed', [
                        'service_order_id' => $serviceOrder->id,
                        'transaction_id' => $serviceOrder->escrowTransaction->id,
                    ]);
                }
            }

            DB::commit();

            Log::info('ServiceOrder: Cancelled successfully', [
                'service_order_id' => $serviceOrder->id,
            ]);

            return redirect()->route('service-orders.index')
                ->with('success', 'Order cancelled and refund has been processed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ServiceOrder: Failed to cancel order', [
                'service_order_id' => $serviceOrder->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }
}
