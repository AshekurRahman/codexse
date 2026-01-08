<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductRequest;
use App\Rules\SecureFileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * Upload a file via AJAX.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120', SecureFileUpload::attachment()],
        ]);

        $file = $request->file('file');

        // Generate a unique filename to prevent overwrites
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('product-requests/temp', $filename, 'public');

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file.',
            ], 500);
        }

        // Get file info
        $isImage = Str::startsWith($file->getMimeType(), 'image/');

        return response()->json([
            'success' => true,
            'file' => [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'url' => asset('storage/' . $path),
                'is_image' => $isImage,
            ],
        ]);
    }

    /**
     * Delete an uploaded file via AJAX.
     */
    public function deleteUpload(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        // Security: Only allow deleting files in the product-requests/temp directory
        if (!Str::startsWith($path, 'product-requests/temp/')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file path.',
            ], 403);
        }

        // Delete the file
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Store a new product request.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
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
            'uploaded_files' => 'nullable|json',
        ], [
            'description.min' => 'Please provide at least 50 characters describing what you need.',
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
        ]);

        // Handle pre-uploaded files (AJAX uploads)
        $attachments = [];
        if (!empty($validated['uploaded_files'])) {
            $uploadedFiles = json_decode($validated['uploaded_files'], true);

            if (is_array($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    // Validate file path
                    if (!isset($file['path']) || !Str::startsWith($file['path'], 'product-requests/temp/')) {
                        continue;
                    }

                    // Move from temp to permanent location
                    $oldPath = $file['path'];
                    $newPath = Str::replace('product-requests/temp/', 'product-requests/', $oldPath);

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->move($oldPath, $newPath);

                        $attachments[] = [
                            'path' => $newPath,
                            'name' => $file['name'] ?? basename($newPath),
                            'size' => $file['size'] ?? Storage::disk('public')->size($newPath),
                        ];
                    }
                }
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your product request has been submitted successfully!',
                'redirect' => route('product-request.success'),
            ]);
        }

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
