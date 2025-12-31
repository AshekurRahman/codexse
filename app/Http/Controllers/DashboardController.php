<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\JobContract;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Product orders
        $totalOrders = $user->orders()->count();
        $totalDownloads = $user->orders()
            ->where('status', 'completed')
            ->withCount('items')
            ->get()
            ->sum('items_count');
        $wishlistCount = $user->wishlists()->count();
        $activeLicenses = $user->licenses()->where('status', 'active')->count();

        // Service orders (as buyer)
        $serviceOrders = ServiceOrder::where('buyer_id', $user->id)->count();

        // Active contracts (as client or seller)
        $activeContracts = JobContract::where(function ($query) use ($user) {
            $query->where('client_id', $user->id)
                  ->orWhere('seller_id', $user->id);
        })->whereIn('status', ['active', 'in_progress'])->count();

        // Job posts (as client)
        $jobPosts = JobPosting::where('client_id', $user->id)->count();

        // Recent product orders
        $recentOrders = $user->orders()
            ->with(['items.product', 'items.license'])
            ->latest()
            ->take(5)
            ->get();

        // Recent service orders (as buyer)
        $recentServiceOrders = ServiceOrder::where('buyer_id', $user->id)
            ->with(['service', 'seller.user'])
            ->latest()
            ->take(5)
            ->get();

        // Recent contracts
        $recentContracts = JobContract::where(function ($query) use ($user) {
            $query->where('client_id', $user->id)
                  ->orWhere('seller_id', $user->id);
        })
            ->with(['jobPosting', 'client', 'seller.user'])
            ->latest()
            ->take(5)
            ->get();

        // Recent job posts
        $recentJobPosts = JobPosting::where('client_id', $user->id)
            ->withCount('proposals')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalOrders',
            'totalDownloads',
            'wishlistCount',
            'activeLicenses',
            'serviceOrders',
            'activeContracts',
            'jobPosts',
            'recentOrders',
            'recentServiceOrders',
            'recentContracts',
            'recentJobPosts'
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
