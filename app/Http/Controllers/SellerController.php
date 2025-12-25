<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\SellerFollow;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::where('status', 'approved')
            ->withCount('products')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->orderByDesc('total_sales')
            ->paginate(12);

        return view('pages.sellers', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        if ($seller->status !== 'approved') {
            abort(404);
        }

        $products = $seller->products()
            ->where('status', 'approved')
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('pages.seller', compact('seller', 'products'));
    }

    public function follow(Seller $seller)
    {
        $user = auth()->user();

        $existing = SellerFollow::where('user_id', $user->id)
            ->where('seller_id', $seller->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            SellerFollow::create([
                'user_id' => $user->id,
                'seller_id' => $seller->id,
            ]);
            $following = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['following' => $following]);
        }

        return back()->with('success', $following ? 'You are now following this seller.' : 'You have unfollowed this seller.');
    }
}
