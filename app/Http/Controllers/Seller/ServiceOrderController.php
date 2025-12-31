<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ServiceDelivery;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceOrderController extends Controller
{
    /**
     * List seller's service orders.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = ServiceOrder::where('seller_id', $seller->id)
            ->with(['service', 'buyer', 'package']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Active orders first, then by date
        $orders = $query->orderByRaw("FIELD(status, 'revision_requested', 'in_progress', 'ordered', 'pending_requirements', 'delivered', 'completed', 'cancelled') ASC")
            ->latest()
            ->paginate(10);

        return view('seller.service-orders.index', compact('orders'));
    }

    /**
     * Show order details.
     */
    public function show(ServiceOrder $serviceOrder)
    {
        $this->authorizeOrder($serviceOrder);

        $serviceOrder->load([
            'service',
            'buyer',
            'package',
            'deliveries' => function ($q) {
                $q->latest();
            },
            'conversation.messages' => function ($q) {
                $q->with('sender')->latest()->limit(20);
            },
            'escrowTransaction'
        ]);

        return view('seller.service-orders.show', compact('serviceOrder'));
    }

    /**
     * Accept/Start working on an order.
     */
    public function start(ServiceOrder $serviceOrder)
    {
        $this->authorizeOrder($serviceOrder);

        if (!$serviceOrder->canStart()) {
            return redirect()->back()->with('error', 'This order cannot be started.');
        }

        $serviceOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'due_at' => now()->addDays($serviceOrder->delivery_days),
        ]);

        // Create conversation if it doesn't exist
        if (!$serviceOrder->conversation_id) {
            $conversation = Conversation::create([
                'buyer_id' => $serviceOrder->buyer_id,
                'seller_id' => $serviceOrder->seller_id,
                'conversationable_type' => ServiceOrder::class,
                'conversationable_id' => $serviceOrder->id,
                'type' => 'service_order',
                'subject' => 'Order: ' . $serviceOrder->order_number,
            ]);

            $serviceOrder->update(['conversation_id' => $conversation->id]);

            // Send system message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'body' => 'Order started. I will begin working on your project.',
                'message_type' => 'system',
                'metadata' => ['action' => 'order_started'],
            ]);
        }

        return redirect()->route('seller.service-orders.show', $serviceOrder)
            ->with('success', 'Order started! The due date has been set.');
    }

    /**
     * Deliver the order.
     */
    public function deliver(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeOrder($serviceOrder);

        if (!$serviceOrder->canDeliver()) {
            return redirect()->back()->with('error', 'This order cannot be delivered.');
        }

        $request->validate([
            'notes' => 'required|string|max:5000',
            'files.*' => 'nullable|file|max:51200', // 50MB per file
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $files = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('deliveries/' . $serviceOrder->id, 'public');
                    $files[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            // Create delivery message
            $message = Message::create([
                'conversation_id' => $serviceOrder->conversation_id,
                'sender_id' => auth()->id(),
                'body' => $request->input('notes'),
                'message_type' => 'delivery',
                'metadata' => [
                    'files' => $files,
                    'action' => 'delivery_submitted',
                ],
            ]);

            // Create delivery record
            $delivery = ServiceDelivery::create([
                'service_order_id' => $serviceOrder->id,
                'message_id' => $message->id,
                'notes' => $request->input('notes'),
                'files' => $files,
                'status' => 'pending',
                'delivered_at' => now(),
            ]);

            // Update order status
            $serviceOrder->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'auto_complete_at' => now()->addDays(3), // Auto-complete after 3 days
            ]);

            DB::commit();

            return redirect()->route('seller.service-orders.show', $serviceOrder)
                ->with('success', 'Delivery submitted! Waiting for buyer review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit delivery. Please try again.');
        }
    }

    /**
     * Request order cancellation (requires buyer approval or dispute).
     */
    public function requestCancellation(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeOrder($serviceOrder);

        if (!in_array($serviceOrder->status, ['ordered', 'in_progress'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled at this stage.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Send cancellation request message
        if ($serviceOrder->conversation_id) {
            Message::create([
                'conversation_id' => $serviceOrder->conversation_id,
                'sender_id' => auth()->id(),
                'body' => "Cancellation requested: " . $request->input('reason'),
                'message_type' => 'system',
                'metadata' => [
                    'action' => 'cancellation_requested',
                    'reason' => $request->input('reason'),
                ],
            ]);
        }

        // Note: Actual cancellation requires buyer approval or dispute resolution
        return redirect()->route('seller.service-orders.show', $serviceOrder)
            ->with('info', 'Cancellation request sent to buyer.');
    }

    /**
     * Extend delivery time.
     */
    public function extendDelivery(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeOrder($serviceOrder);

        if (!in_array($serviceOrder->status, ['in_progress', 'revision_requested'])) {
            return redirect()->back()->with('error', 'Cannot extend delivery for this order.');
        }

        $request->validate([
            'extra_days' => 'required|integer|min:1|max:30',
            'reason' => 'required|string|max:500',
        ]);

        $newDueAt = $serviceOrder->due_at
            ? $serviceOrder->due_at->addDays($request->input('extra_days'))
            : now()->addDays($request->input('extra_days'));

        $serviceOrder->update(['due_at' => $newDueAt]);

        // Notify buyer
        if ($serviceOrder->conversation_id) {
            Message::create([
                'conversation_id' => $serviceOrder->conversation_id,
                'sender_id' => auth()->id(),
                'body' => "Delivery extended by {$request->input('extra_days')} days. Reason: {$request->input('reason')}",
                'message_type' => 'system',
                'metadata' => [
                    'action' => 'delivery_extended',
                    'extra_days' => $request->input('extra_days'),
                    'new_due_date' => $newDueAt->toDateString(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Delivery date extended.');
    }

    protected function authorizeOrder(ServiceOrder $serviceOrder): void
    {
        if ($serviceOrder->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }
    }
}
