<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\JobContract;
use App\Models\JobPosting;
use App\Models\Order;
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

        // ===== Chart Data =====

        // Order Status Distribution (Pie Chart)
        $orderStatusData = $user->orders()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Service Order Status Distribution (Pie Chart)
        $serviceStatusData = ServiceOrder::where('buyer_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Contract Status Distribution (Pie Chart)
        $contractStatusData = JobContract::where(function ($query) use ($user) {
            $query->where('client_id', $user->id)
                  ->orWhere('seller_id', $user->id);
        })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Spending Overview (Monthly - last 6 months)
        $monthlySpending = $user->orders()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Total spending
        $totalSpent = $user->orders()->where('status', 'completed')->sum('total');
        $totalServiceSpent = ServiceOrder::where('buyer_id', $user->id)
            ->whereIn('status', ['completed', 'delivered'])
            ->sum('price');

        // Activity breakdown for donut chart
        $activityBreakdown = [
            'products' => $totalOrders,
            'services' => $serviceOrders,
            'contracts' => $activeContracts,
            'jobs' => $jobPosts,
        ];

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
            'recentJobPosts',
            'orderStatusData',
            'serviceStatusData',
            'contractStatusData',
            'monthlySpending',
            'totalSpent',
            'totalServiceSpent',
            'activityBreakdown'
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
