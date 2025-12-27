<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get product details for quick view modal.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product || $product->status !== 'published') {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Get gallery images from preview_images
        $gallery = [];
        if ($product->preview_images) {
            foreach ($product->preview_images as $image) {
                if (is_array($image) && isset($image['path'])) {
                    $gallery[] = asset('storage/' . $image['path']);
                } elseif (is_string($image)) {
                    $gallery[] = asset('storage/' . $image);
                }
            }
        }

        // Check if in wishlist
        $inWishlist = false;
        if (auth()->check()) {
            $inWishlist = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
        }

        // Get tags from relationship
        $tags = $product->tags()->pluck('name')->toArray();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'description' => \Str::limit(strip_tags($product->description), 300),
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'thumbnail' => $product->thumbnail_url,
            'gallery' => $gallery,
            'category' => $product->category?->name,
            'rating' => round($product->average_rating, 1),
            'reviews_count' => $product->reviews()->approved()->count(),
            'sales' => $product->downloads_count,
            'tags' => $tags,
            'url' => route('products.show', $product),
            'in_wishlist' => $inWishlist,
            'seller' => $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->store_name,
                'avatar' => $product->seller->logo ? asset('storage/' . $product->seller->logo) : null,
            ] : null,
        ]);
    }
}
