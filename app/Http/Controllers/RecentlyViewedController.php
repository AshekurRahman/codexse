<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class RecentlyViewedController extends Controller
{
    /**
     * Maximum number of recently viewed products to store.
     */
    const MAX_ITEMS = 12;

    /**
     * Get recently viewed products.
     */
    public function index()
    {
        $productIds = session('recently_viewed', []);
        $products = Product::with(['seller', 'category'])
            ->whereIn('id', $productIds)
            ->published()
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            });

        return response()->json([
            'success' => true,
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'thumbnail' => $product->thumbnail_url,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'current_price' => $product->current_price,
                    'category' => $product->category?->name,
                    'average_rating' => $product->average_rating,
                    'url' => route('products.show', $product),
                ];
            })->values(),
        ]);
    }

    /**
     * Add a product to recently viewed.
     */
    public static function trackProduct(Product $product): void
    {
        $recentlyViewed = session('recently_viewed', []);

        // Remove if already exists (to move to front)
        if (($key = array_search($product->id, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }

        // Add to beginning
        array_unshift($recentlyViewed, $product->id);

        // Limit to max items
        $recentlyViewed = array_slice($recentlyViewed, 0, self::MAX_ITEMS);

        session(['recently_viewed' => $recentlyViewed]);
    }

    /**
     * Clear recently viewed products.
     */
    public function clear()
    {
        session()->forget('recently_viewed');

        return response()->json([
            'success' => true,
            'message' => 'Recently viewed products cleared',
        ]);
    }

    /**
     * Get recently viewed products for display (non-API).
     */
    public static function getProducts(int $limit = 8, ?int $excludeId = null): \Illuminate\Support\Collection
    {
        $productIds = session('recently_viewed', []);

        // Exclude current product if provided
        if ($excludeId) {
            $productIds = array_filter($productIds, fn($id) => $id !== $excludeId);
        }

        // Limit the IDs
        $productIds = array_slice($productIds, 0, $limit);

        if (empty($productIds)) {
            return collect();
        }

        return Product::with(['seller', 'category'])
            ->whereIn('id', $productIds)
            ->published()
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            })
            ->values();
    }
}
