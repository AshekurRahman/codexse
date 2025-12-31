<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomQuote;
use App\Models\CustomQuoteRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomQuoteController extends Controller
{
    /**
     * List quote requests for the seller.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = CustomQuoteRequest::where('seller_id', $seller->id)
            ->with(['service', 'buyer', 'quote']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pending first
        $quoteRequests = $query->orderByRaw("FIELD(status, 'pending', 'quoted', 'accepted', 'rejected', 'expired') ASC")
            ->latest()
            ->paginate(10);

        return view('seller.quotes.index', compact('quoteRequests'));
    }

    /**
     * Show a quote request.
     */
    public function show(CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        $quoteRequest->load(['service', 'buyer', 'quote', 'conversation.messages']);

        return view('seller.quotes.show', compact('quoteRequest'));
    }

    /**
     * Show form to create a quote.
     */
    public function createQuote(CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        if ($quoteRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot create quote for this request.');
        }

        $quoteRequest->load(['service', 'buyer']);

        return view('seller.quotes.create-quote', compact('quoteRequest'));
    }

    /**
     * Submit a quote for a request.
     */
    public function storeQuote(Request $request, CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        if ($quoteRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot create quote for this request.');
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:5',
            'delivery_days' => 'required|integer|min:1|max:365',
            'revisions' => 'nullable|integer|min:0|max:99',
            'description' => 'required|string|max:5000',
            'deliverables' => 'nullable|array',
            'deliverables.*' => 'string|max:200',
            'expires_in_days' => 'required|integer|min:1|max:30',
        ]);

        try {
            DB::beginTransaction();

            // Create the quote
            $quote = CustomQuote::create([
                'custom_quote_request_id' => $quoteRequest->id,
                'price' => $validated['price'],
                'delivery_days' => $validated['delivery_days'],
                'revisions' => $validated['revisions'] ?? 0,
                'description' => $validated['description'],
                'deliverables' => $validated['deliverables'] ?? [],
                'expires_at' => now()->addDays($validated['expires_in_days']),
                'status' => 'pending',
            ]);

            // Update quote request status
            $quoteRequest->update(['status' => 'quoted']);

            // Send message to conversation
            if ($quoteRequest->conversation_id) {
                Message::create([
                    'conversation_id' => $quoteRequest->conversation_id,
                    'sender_id' => auth()->id(),
                    'body' => "I've sent you a quote for $" . number_format($validated['price'], 2) . " with {$validated['delivery_days']} days delivery.",
                    'message_type' => 'system',
                    'metadata' => [
                        'action' => 'quote_submitted',
                        'quote_id' => $quote->id,
                        'price' => $validated['price'],
                        'delivery_days' => $validated['delivery_days'],
                    ],
                ]);
            }

            DB::commit();

            return redirect()->route('seller.quotes.show', $quoteRequest)
                ->with('success', 'Quote submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit quote. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update an existing quote.
     */
    public function updateQuote(Request $request, CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        $quote = $quoteRequest->quote;

        if (!$quote || $quote->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot update this quote.');
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:5',
            'delivery_days' => 'required|integer|min:1|max:365',
            'revisions' => 'nullable|integer|min:0|max:99',
            'description' => 'required|string|max:5000',
            'deliverables' => 'nullable|array',
            'deliverables.*' => 'string|max:200',
            'expires_in_days' => 'required|integer|min:1|max:30',
        ]);

        $quote->update([
            'price' => $validated['price'],
            'delivery_days' => $validated['delivery_days'],
            'revisions' => $validated['revisions'] ?? 0,
            'description' => $validated['description'],
            'deliverables' => $validated['deliverables'] ?? [],
            'expires_at' => now()->addDays($validated['expires_in_days']),
        ]);

        return redirect()->route('seller.quotes.show', $quoteRequest)
            ->with('success', 'Quote updated successfully!');
    }

    /**
     * Withdraw/cancel a quote.
     */
    public function withdrawQuote(CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        $quote = $quoteRequest->quote;

        if (!$quote || $quote->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot withdraw this quote.');
        }

        $quote->update(['status' => 'expired']);
        $quoteRequest->update(['status' => 'pending']); // Allow new quote

        return redirect()->route('seller.quotes.index')
            ->with('success', 'Quote withdrawn.');
    }

    /**
     * Decline a quote request.
     */
    public function decline(Request $request, CustomQuoteRequest $quoteRequest)
    {
        $this->authorizeRequest($quoteRequest);

        if ($quoteRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot decline this request.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $quoteRequest->update(['status' => 'rejected']);

        // Send message
        if ($quoteRequest->conversation_id) {
            $message = $request->input('reason')
                ? "I'm unable to fulfill this request. Reason: " . $request->input('reason')
                : "I'm unable to fulfill this request at this time.";

            Message::create([
                'conversation_id' => $quoteRequest->conversation_id,
                'sender_id' => auth()->id(),
                'body' => $message,
                'message_type' => 'system',
                'metadata' => ['action' => 'request_declined'],
            ]);
        }

        return redirect()->route('seller.quotes.index')
            ->with('success', 'Quote request declined.');
    }

    protected function authorizeRequest(CustomQuoteRequest $quoteRequest): void
    {
        if ($quoteRequest->seller_id !== auth()->user()->seller->id) {
            abort(403);
        }
    }
}
