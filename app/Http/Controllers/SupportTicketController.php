<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->with(['product', 'order'])
            ->latest()
            ->paginate(20);

        return view('pages.support.index', compact('tickets'));
    }

    public function create()
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->get();

        return view('pages.support.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|in:general,technical,billing,refund,other',
            'priority' => 'required|in:low,medium,high',
            'order_id' => 'nullable|exists:orders,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'order_id' => $validated['order_id'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Support ticket created successfully. We will respond shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies.user', 'product', 'order']);

        return view('pages.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$ticket->isOpen()) {
            return back()->with('error', 'This ticket is closed.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff' => false,
        ]);

        $ticket->update(['status' => 'waiting']);

        return back()->with('success', 'Reply added successfully.');
    }

    public function close(SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->close();

        return back()->with('success', 'Ticket closed successfully.');
    }
}
