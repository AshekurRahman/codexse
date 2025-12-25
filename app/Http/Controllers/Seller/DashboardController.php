<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = auth()->user()->seller;

        $stats = [
            'total_products' => $seller->products()->count(),
            'total_sales' => $seller->orderItems()->sum('price'),
            'total_orders' => $seller->orderItems()->count(),
            'active_licenses' => \App\Models\License::whereIn('product_id', $seller->products()->pluck('id'))->where('status', 'active')->count(),
            'pending_payouts' => $seller->balance,
        ];

        $recentOrders = $seller->orderItems()
            ->with(['order.user', 'product', 'license'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact('seller', 'stats', 'recentOrders'));
    }
}
