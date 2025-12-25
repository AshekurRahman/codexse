<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = collect();
        $total = 0;

        if (!empty($cart)) {
            $productIds = array_keys($cart);
            $products = Product::whereIn('id', $productIds)->with('seller')->get();

            foreach ($products as $product) {
                // Use cart snapshot price if available, otherwise current price
                $cartItem = $cart[$product->id] ?? [];
                $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                $total += $price;
            }
        }

        return view('pages.cart', compact('cart', 'products', 'total'));
    }

    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [
                'license_type' => request('license_type', 'regular'),
                'price' => $product->current_price,
                'added_at' => now()->toDateTimeString(),
            ];
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
            return redirect()->back()->with('success', 'Product removed from cart.');
        }

        return redirect()->back()->with('error', 'Product not found in cart.');
    }
}
