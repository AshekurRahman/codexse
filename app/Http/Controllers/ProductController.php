<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            $query->where(function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                    ->orWhere('sale_price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->whereNull('sale_price')->where('price', '<=', $request->max_price);
                })->orWhere(function ($q2) use ($request) {
                    $q2->whereNotNull('sale_price')->where('sale_price', '<=', $request->max_price);
                });
            });
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Date filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case '3months':
                    $query->where('created_at', '>=', now()->subMonths(3));
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        // On sale filter
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        // Featured filter
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Seller filter
        if ($request->filled('seller')) {
            $query->whereHas('seller', function ($q) use ($request) {
                $q->where('store_slug', $request->seller);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderByDesc('downloads_count');
                break;
            case 'bestselling':
                $query->orderByDesc('sales_count');
                break;
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            case 'trending':
                $query->orderByDesc('views_count');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // Cache categories for 1 hour
        $categories = Cache::remember('product_categories', 3600, function () {
            return Category::withCount(['products' => function ($q) {
                $q->where('status', 'published');
            }])->whereNull('parent_id')->orderBy('name')->get();
        });

        // Cache price stats for 15 minutes
        $priceStats = Cache::remember('product_price_stats', 900, function () {
            return Product::where('status', 'published')
                ->selectRaw('MIN(COALESCE(sale_price, price)) as min_price, MAX(COALESCE(sale_price, price)) as max_price')
                ->first();
        });

        // Active filters count
        $activeFiltersCount = collect([
            $request->filled('category'),
            $request->filled('min_price') || $request->filled('max_price'),
            $request->filled('min_rating'),
            $request->filled('date_range'),
            $request->boolean('on_sale'),
            $request->boolean('featured'),
        ])->filter()->count();

        // Pre-load wishlist IDs for authenticated users (single query)
        $wishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')
            : collect();

        return view('pages.products.index', compact(
            'products',
            'categories',
            'priceStats',
            'activeFiltersCount',
            'wishlistIds'
        ));
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
                    'thumbnail' => $product->thumbnail_url,
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

        // Track recently viewed
        RecentlyViewedController::trackProduct($product);

        $product->load(['seller', 'category', 'reviews.user', 'activeSubscriptionPlans']);

        $relatedProducts = Product::with(['seller', 'category'])
            ->where('status', 'published')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // Get recently viewed products (excluding current)
        $recentlyViewed = RecentlyViewedController::getProducts(8, $product->id);

        return view('pages.products.show', compact('product', 'relatedProducts', 'recentlyViewed'));
    }
}
