<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Rules\SecureFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->seller->products()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|max:2048',
            'file' => ['required', 'file', SecureFileUpload::productFile()],
            'demo_url' => 'nullable|url|max:255',
            'preview_url' => 'nullable|url|max:255',
        ]);

        $seller = auth()->user()->seller;

        $product = $seller->products()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'demo_url' => $validated['demo_url'] ?? null,
            'preview_url' => $validated['preview_url'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->hasFile('thumbnail')) {
            $product->addMedia($request->file('thumbnail'))->toMediaCollection('thumbnail');
        }

        if ($request->hasFile('file')) {
            $product->addMedia($request->file('file'))->toMediaCollection('files');
        }

        return redirect()->route('seller.products.index')->with('success', 'Product created successfully and is pending review.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return view('seller.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'file' => ['nullable', 'file', SecureFileUpload::productFile()],
            'demo_url' => 'nullable|url|max:255',
            'preview_url' => 'nullable|url|max:255',
            'has_variations' => 'nullable|boolean',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.description' => 'nullable|string',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.regular_price' => 'nullable|numeric|min:0',
            'variations.*.features' => 'nullable|array',
            'variations.*.license_type' => 'nullable|string|in:regular,extended',
            'variations.*.support_months' => 'nullable|integer|min:0',
            'variations.*.updates_months' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.sort_order' => 'nullable|integer',
        ]);

        $hasVariations = $request->boolean('has_variations');

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'demo_url' => $validated['demo_url'] ?? null,
            'preview_url' => $validated['preview_url'] ?? null,
            'has_variations' => $hasVariations,
        ]);

        // Handle variations
        if ($hasVariations && !empty($request->variations)) {
            $existingIds = [];

            foreach ($request->variations as $index => $variationData) {
                $variationId = $variationData['id'] ?? null;

                $data = [
                    'name' => $variationData['name'],
                    'slug' => Str::slug($variationData['name']),
                    'description' => $variationData['description'] ?? null,
                    'price' => $variationData['price'],
                    'regular_price' => $variationData['regular_price'] ?: null,
                    'features' => $variationData['features'] ?? [],
                    'license_type' => $variationData['license_type'] ?? 'regular',
                    'support_months' => $variationData['support_months'] ?? 6,
                    'updates_months' => $variationData['updates_months'] ?? 12,
                    'is_default' => (bool) ($variationData['is_default'] ?? false),
                    'is_active' => (bool) ($variationData['is_active'] ?? true),
                    'sort_order' => $variationData['sort_order'] ?? $index,
                ];

                if ($variationId) {
                    $variation = $product->variations()->find($variationId);
                    if ($variation) {
                        $variation->update($data);
                        $existingIds[] = $variation->id;
                    }
                } else {
                    $variation = $product->variations()->create($data);
                    $existingIds[] = $variation->id;
                }
            }

            // Delete removed variations
            $product->variations()->whereNotIn('id', $existingIds)->delete();
        } elseif (!$hasVariations) {
            // Remove all variations if disabled
            $product->variations()->delete();
        }

        if ($request->hasFile('thumbnail')) {
            $product->clearMediaCollection('thumbnail');
            $product->addMedia($request->file('thumbnail'))->toMediaCollection('thumbnail');
        }

        if ($request->hasFile('file')) {
            $product->clearMediaCollection('files');
            $product->addMedia($request->file('file'))->toMediaCollection('files');
        }

        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully.');
    }
}
