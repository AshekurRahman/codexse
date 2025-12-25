<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['seller', 'category'])
            ->where('status', 'published');

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderByDesc('downloads_count');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->get();

        return view('pages.products.index', compact('products', 'categories'));
    }

    /**
     * Search suggestions for autocomplete.
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%");
            })
            ->with('category')
            ->select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'category_id')
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'url' => route('products.show', $product),
                    'price' => $product->sale_price ?? $product->price,
                    'original_price' => $product->sale_price ? $product->price : null,
                    'thumbnail' => $product->thumbnail ? $product->thumbnail_url : null,
                    'category' => $product->category?->name,
                ];
            });

        $categories = Category::where('name', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'url' => route('categories.show', $category),
                    'type' => 'category',
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Display a specific product.
     */
    public function show(Product $product): View
    {
        // Increment view count
        $product->increment('views_count');

        $product->load(['seller', 'category', 'reviews.user']);

        $relatedProducts = Product::with(['seller', 'category'])
            ->where('status', 'published')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('pages.products.show', compact('product', 'relatedProducts'));
    }
}
