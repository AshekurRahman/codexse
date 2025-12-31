<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\CustomQuote;
use App\Models\CustomQuoteRequest;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomQuoteController extends Controller
{
    /**
     * Show the custom quote request form.
     */
    public function create(Service $service)
    {
        if ($service->status !== 'published') {
            abort(404);
        }

        // Don't allow sellers to request quotes from themselves
        if (auth()->user()->seller && auth()->user()->seller->id === $service->seller_id) {
            return redirect()->route('services.show', $service)
                ->with('error', 'You cannot request a quote from yourself.');
        }

        return view('pages.quotes.create', compact('service'));
    }

    /**
     * Submit a custom quote request.
     */
    public function store(Request $request, Service $service)
    {
        if ($service->status !== 'published') {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('quote-requests/' . auth()->id(), 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            // Create or get conversation
            $conversation = Conversation::firstOrCreate([
                'buyer_id' => auth()->id(),
                'seller_id' => $service->seller_id,
                'type' => 'general',
            ], [
                'subject' => 'Quote Request: ' . $validated['title'],
            ]);

            $quoteRequest = CustomQuoteRequest::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $service->seller_id,
                'service_id' => $service->id,
                'conversation_id' => $conversation->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'budget_min' => $validated['budget_min'],
                'budget_max' => $validated['budget_max'],
                'deadline' => $validated['deadline'],
                'attachments' => $attachments,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('quotes.show', $quoteRequest)
                ->with('success', 'Quote request submitted successfully. The seller will respond soon.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit quote request. Please try again.')
                ->withInput();
        }
    }

    /**
     * List buyer's quote requests.
     */
    public function index(Request $request)
    {
        $query = CustomQuoteRequest::where('buyer_id', auth()->id())
            ->with(['service', 'seller', 'quote']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quoteRequests = $query->latest()->paginate(10);

        return view('pages.quotes.index', compact('quoteRequests'));
    }

    /**
     * Show a single quote request.
     */
    public function show(CustomQuoteRequest $quoteRequest)
    {
        // Only buyer or seller can view
        if ($quoteRequest->buyer_id !== auth()->id() &&
            (!auth()->user()->seller || auth()->user()->seller->id !== $quoteRequest->seller_id)) {
            abort(403);
        }

        $quoteRequest->load(['service', 'seller', 'quote', 'conversation.messages']);

        return view('pages.quotes.show', compact('quoteRequest'));
    }

    /**
     * Accept a quote and create an order.
     */
    public function accept(CustomQuoteRequest $quoteRequest, EscrowService $escrowService)
    {
        if ($quoteRequest->buyer_id !== auth()->id()) {
            abort(403);
        }

        $quote = $quoteRequest->quote;

        if (!$quote || !$quote->canAccept()) {
            return redirect()->back()->with('error', 'This quote cannot be accepted.');
        }

        try {
            DB::beginTransaction();

            $fees = $escrowService->calculateFees($quote->price, 'service');

            // Create service order from quote
            $order = ServiceOrder::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $quoteRequest->seller_id,
                'service_id' => $quoteRequest->service_id,
                'conversation_id' => $quoteRequest->conversation_id,
                'title' => $quoteRequest->title,
                'description' => $quote->description,
                'price' => $quote->price,
                'platform_fee' => $fees['platform_fee'],
                'seller_amount' => $fees['seller_amount'],
                'delivery_days' => $quote->delivery_days,
                'revisions_allowed' => $quote->revisions,
                'status' => 'pending_payment',
                'requirements_data' => [
                    'quote_request' => [
                        'description' => $quoteRequest->description,
                        'attachments' => $quoteRequest->attachments,
                    ],
                ],
            ]);

            // Update quote and request status
            $quote->update(['status' => 'accepted']);
            $quoteRequest->update(['status' => 'accepted']);

            DB::commit();

            return redirect()->route('escrow.checkout.service-order', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to accept quote. Please try again.');
        }
    }

    /**
     * Reject a quote.
     */
    public function reject(Request $request, CustomQuoteRequest $quoteRequest)
    {
        if ($quoteRequest->buyer_id !== auth()->id()) {
            abort(403);
        }

        $quote = $quoteRequest->quote;

        if (!$quote || $quote->status !== 'pending') {
            return redirect()->back()->with('error', 'This quote cannot be rejected.');
        }

        $quote->update(['status' => 'rejected']);
        $quoteRequest->update(['status' => 'rejected']);

        return redirect()->route('quotes.index')
            ->with('success', 'Quote rejected.');
    }
}
