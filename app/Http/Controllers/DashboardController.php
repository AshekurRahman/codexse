<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalOrders = $user->orders()->count();
        $totalDownloads = $user->orders()
            ->where('status', 'completed')
            ->withCount('items')
            ->get()
            ->sum('items_count');
        $wishlistCount = $user->wishlists()->count();
        $activeLicenses = $user->licenses()->where('status', 'active')->count();

        $recentOrders = $user->orders()
            ->with(['items.product', 'items.license'])
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalOrders',
            'totalDownloads',
            'wishlistCount',
            'activeLicenses',
            'recentOrders'
        ));
    }

    public function purchases()
    {
        $orders = auth()->user()->orders()->with(['items.product', 'items.license'])->latest()->paginate(10);
        return view('pages.purchases', compact('orders'));
    }

    public function wishlist()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->latest()->paginate(12);
        return view('pages.wishlist', compact('wishlists'));
    }
}
