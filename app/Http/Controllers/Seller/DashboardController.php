<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomQuoteRequest;
use App\Models\License;
use App\Models\ServiceOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = auth()->user()->seller;

        $wallet = auth()->user()->getOrCreateWallet();

        // Optimized: Single query for product/service counts using withCount
        $seller->loadCount([
            'products',
            'services',
            'orderItems',
            'jobContracts as active_contracts_count' => function ($query) {
                $query->where('status', 'active');
            }
        ]);

        // Optimized: Single query for order items aggregates
        $orderStats = $seller->orderItems()
            ->selectRaw('COALESCE(SUM(price), 0) as total_sales')
            ->first();

        // Optimized: Get product IDs once, use for license count
        $productIds = $seller->products()->pluck('id');
        $activeLicenses = $productIds->isNotEmpty()
            ? License::whereIn('product_id', $productIds)->where('status', 'active')->count()
            : 0;

        $stats = [
            'total_products' => $seller->products_count,
            'total_services' => $seller->services_count,
            'total_sales' => $orderStats->total_sales ?? 0,
            'total_orders' => $seller->order_items_count,
            'active_licenses' => $activeLicenses,
            'active_contracts' => $seller->active_contracts_count,
            'pending_payouts' => $wallet->balance,
            'average_rating' => $seller->rating ?? 0,
        ];

        $recentOrders = $seller->orderItems()
            ->with(['order.user', 'product', 'license'])
            ->latest()
            ->take(5)
            ->get();

        // Get pending quote requests for seller's services
        $quoteRequests = CustomQuoteRequest::whereHas('service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
            ->with(['service', 'buyer'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Get recent service orders
        $recentServiceOrders = ServiceOrder::where('seller_id', $seller->id)
            ->with(['service', 'buyer'])
            ->latest()
            ->take(5)
            ->get();

        // Get active contracts
        $activeContracts = $seller->jobContracts()
            ->where('status', 'active')
            ->with(['jobPosting.client', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // Recent wallet transactions
        $recentWalletTransactions = $wallet->transactions()
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'seller',
            'stats',
            'recentOrders',
            'quoteRequests',
            'recentServiceOrders',
            'activeContracts',
            'wallet',
            'recentWalletTransactions'
        ));
    }

    public function updateVacationMode(Request $request)
    {
        $seller = auth()->user()->seller;

        $validated = $request->validate([
            'is_on_vacation' => 'nullable|boolean',
            'vacation_message' => 'nullable|string|max:500',
            'vacation_ends_at' => 'nullable|date|after:today',
            'vacation_auto_reply' => 'nullable|boolean',
        ]);

        $isOnVacation = $request->boolean('is_on_vacation');

        if ($isOnVacation) {
            $seller->update([
                'is_on_vacation' => true,
                'vacation_message' => $validated['vacation_message'] ?? null,
                'vacation_started_at' => $seller->is_on_vacation ? $seller->vacation_started_at : now(),
                'vacation_ends_at' => $validated['vacation_ends_at'] ? Carbon::parse($validated['vacation_ends_at'])->endOfDay() : null,
                'vacation_auto_reply' => $request->boolean('vacation_auto_reply'),
            ]);

            return redirect()->route('seller.dashboard')->with('success', 'Vacation mode enabled. Your store is now paused.');
        } else {
            $seller->disableVacationMode();

            return redirect()->route('seller.dashboard')->with('success', 'Welcome back! Vacation mode has been disabled.');
        }
    }
}
