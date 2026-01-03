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
            ->withCount([
                'products' => function ($query) {
                    $query->where('status', 'published');
                },
                'services' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
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

        // Load followers count
        $seller->loadCount('followers');

        $products = $seller->products()
            ->where('status', 'approved')
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $services = $seller->services()
            ->where('status', 'published')
            ->with(['category', 'packages'])
            ->latest()
            ->take(8)
            ->get();

        $totalProducts = $seller->products()->where('status', 'approved')->count();
        $totalServices = $seller->services()->where('status', 'published')->count();

        return view('pages.seller', compact('seller', 'products', 'services', 'totalProducts', 'totalServices'));
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
            $message = 'You have unfollowed this seller.';
        } else {
            SellerFollow::create([
                'user_id' => $user->id,
                'seller_id' => $seller->id,
            ]);
            $following = true;
            $message = 'You are now following this seller.';
        }

        // Get updated followers count
        $followersCount = $seller->followers()->count();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'following' => $following,
                'followers_count' => $followersCount,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
