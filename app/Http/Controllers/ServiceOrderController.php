<?php

namespace App\Http\Controllers;

use App\Models\ServiceDelivery;
use App\Models\ServiceOrder;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $rules[$key] .= '|file|max:10240';
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
            abort(403);
        }

        if (!$serviceOrder->canApprove()) {
            return redirect()->back()->with('error', 'This order cannot be approved.');
        }

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
                $escrowService->releaseFunds($serviceOrder->escrowTransaction, 'Buyer approved delivery');
            }

            DB::commit();

            return redirect()->route('service-orders.show', $serviceOrder)
                ->with('success', 'Order completed! Payment has been released to the seller.');

        } catch (\Exception $e) {
            DB::rollBack();
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
            abort(403);
        }

        if (!$serviceOrder->canCancel()) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
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
                $escrowService->refundFunds($serviceOrder->escrowTransaction, 'Order cancelled by buyer');
            }

            DB::commit();

            return redirect()->route('service-orders.index')
                ->with('success', 'Order cancelled and refund has been processed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }
}
