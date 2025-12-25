<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return $this->errorResponse('Invalid coupon code.');
        }

        if (!$coupon->isValid()) {
            return $this->errorResponse('This coupon is no longer valid.');
        }

        // Check user usage limit
        if (auth()->check() && $coupon->max_uses_per_user) {
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', auth()->id())
                ->count();

            if ($userUsage >= $coupon->max_uses_per_user) {
                return $this->errorResponse('You have already used this coupon.');
            }
        }

        // Calculate discount
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);

        if ($coupon->minimum_order && $subtotal < $coupon->minimum_order) {
            return $this->errorResponse("Minimum order amount is $" . number_format($coupon->minimum_order, 2));
        }

        $discount = $coupon->calculateDiscount($subtotal);

        // Store coupon in session
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'coupon' => session('coupon'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $subtotal - $discount,
            ]);
        }

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function remove(Request $request)
    {
        session()->forget('coupon');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed.',
            ]);
        }

        return back()->with('success', 'Coupon removed.');
    }

    private function calculateSubtotal(array $cart): float
    {
        $subtotal = 0;

        if (!empty($cart)) {
            $productIds = array_keys($cart);
            $products = \App\Models\Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                $cartItem = $cart[$product->id] ?? [];
                $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                $subtotal += $price;
            }
        }

        return $subtotal;
    }

    private function errorResponse(string $message)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return back()->with('error', $message);
    }
}
