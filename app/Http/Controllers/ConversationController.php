<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('buyer_id', auth()->id())
            ->with(['seller.user', 'product', 'latestMessage'])
            ->latest('last_message_at')
            ->paginate(20);

        return view('pages.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->buyer_id !== auth()->id()) {
            abort(403);
        }

        $conversation->load(['seller.user', 'product', 'messages.sender']);
        $conversation->markAsReadFor(auth()->user());

        return view('pages.conversations.show', compact('conversation'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'product_id' => 'nullable|exists:products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $validated['seller_id'],
            'product_id' => $validated['product_id'] ?? null,
            'subject' => $validated['subject'],
            'last_message_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        return redirect()->route('conversations.show', $conversation)
            ->with('success', 'Message sent successfully!');
    }

    public function reply(Request $request, Conversation $conversation)
    {
        if ($conversation->buyer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Reply sent!');
    }

    public function create(Request $request)
    {
        $seller = null;
        $product = null;

        if ($request->has('seller')) {
            $seller = Seller::findOrFail($request->seller);
        }

        if ($request->has('product')) {
            $product = Product::findOrFail($request->product);
            $seller = $product->seller;
        }

        return view('pages.conversations.create', compact('seller', 'product'));
    }
}
