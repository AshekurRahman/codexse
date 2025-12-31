<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = collect();
        $total = 0;

        if (!empty($cart)) {
            // Get all unique product IDs from cart
            $productIds = collect($cart)->pluck('product_id')->filter()->unique()->toArray();

            // For backward compatibility, also check numeric keys
            foreach ($cart as $key => $item) {
                if (is_numeric($key) && !in_array($key, $productIds)) {
                    $productIds[] = $key;
                }
            }

            $products = Product::whereIn('id', $productIds)->with('seller', 'variations')->get()->keyBy('id');

            foreach ($cart as $cartKey => $cartItem) {
                // Handle both old and new cart formats
                $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
                $product = $products->get($productId);

                if ($product) {
                    $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                    $variationName = is_array($cartItem) ? ($cartItem['variation_name'] ?? null) : null;

                    $cartItems->push([
                        'cart_key' => $cartKey,
                        'product' => $product,
                        'price' => $price,
                        'variation_id' => is_array($cartItem) ? ($cartItem['variation_id'] ?? null) : null,
                        'variation_name' => $variationName,
                        'license_type' => is_array($cartItem) ? ($cartItem['license_type'] ?? 'regular') : 'regular',
                    ]);

                    $total += $price;
                }
            }
        }

        return view('pages.cart', compact('cart', 'cartItems', 'total'));
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);
        $variationId = request('variation_id');
        $variation = null;

        // Check if product has variations and a variation was selected
        if ($product->has_variations && $variationId) {
            $variation = $product->variations()->where('id', $variationId)->where('is_active', true)->first();
        }

        // Generate unique cart key (product_id or product_id_variation_id)
        $cartKey = $variation ? $product->id . '_' . $variation->id : $product->id;

        if (!isset($cart[$cartKey])) {
            $cartItem = [
                'product_id' => $product->id,
                'added_at' => now()->toDateTimeString(),
            ];

            if ($variation) {
                $cartItem['variation_id'] = $variation->id;
                $cartItem['variation_name'] = $variation->name;
                $cartItem['price'] = $variation->price;
                $cartItem['license_type'] = $variation->license_type;
                $cartItem['support_months'] = $variation->support_months;
                $cartItem['updates_months'] = $variation->updates_months;
            } else {
                $cartItem['price'] = $product->current_price;
                $cartItem['license_type'] = request('license_type', 'regular');
            }

            $cart[$cartKey] = $cartItem;
            session()->put('cart', $cart);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart!',
                    'cart_count' => count($cart),
                ]);
            }

            return redirect()->back()->with('success', 'Product added to cart!');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your cart.',
                'cart_count' => count($cart),
            ]);
        }

        return redirect()->back()->with('info', 'Product is already in your cart.');
    }

    public function remove($item)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$item])) {
            unset($cart[$item]);
            session()->put('cart', $cart);

            // Calculate new totals
            $subtotal = 0;
            foreach ($cart as $cartKey => $cartItem) {
                $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
                $product = Product::find($productId);
                if ($product) {
                    $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                    $subtotal += $price;
                }
            }
            $discount = session('coupon.discount', 0);
            $total = $subtotal - $discount;

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart.',
                    'cart_count' => count($cart),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                ]);
            }

            return redirect()->back()->with('success', 'Product removed from cart.');
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart.',
            ], 404);
        }

        return redirect()->back()->with('error', 'Product not found in cart.');
    }
}
