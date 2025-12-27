<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductRequestController extends Controller
{
    /**
     * Show the product request form.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('pages.product-request.create', compact('categories'));
    }

    /**
     * Store a new product request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'product_title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string|min:50|max:5000',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'urgency' => 'required|in:low,normal,high,urgent',
            'features' => 'nullable|string|max:2000',
            'reference_urls' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx,zip',
        ], [
            'description.min' => 'Please provide at least 50 characters describing what you need.',
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('product-requests', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        // Create the product request
        ProductRequest::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'product_title' => $validated['product_title'],
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
            'urgency' => $validated['urgency'],
            'features' => $validated['features'] ?? null,
            'reference_urls' => $validated['reference_urls'] ?? null,
            'attachments' => !empty($attachments) ? $attachments : null,
            'status' => ProductRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('product-request.success')
            ->with('success', 'Your product request has been submitted successfully! We will review it and get back to you soon.');
    }

    /**
     * Show success page after submission.
     */
    public function success(): View
    {
        return view('pages.product-request.success');
    }

    /**
     * Show user's product requests (for authenticated users).
     */
    public function index(): View
    {
        $requests = ProductRequest::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('pages.product-request.index', compact('requests'));
    }

    /**
     * Show a specific product request (for authenticated users).
     */
    public function show(ProductRequest $productRequest): View
    {
        // Ensure user can only view their own requests
        if ($productRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $productRequest->load(['category', 'fulfilledByProduct']);

        return view('pages.product-request.show', compact('productRequest'));
    }
}
