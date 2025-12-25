<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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
            'file' => 'required|file|max:102400', // 100MB
        ]);

        $seller = auth()->user()->seller;

        $product = $seller->products()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
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
            'file' => 'nullable|file|max:102400',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
        ]);

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
