<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request): View
    {
        $seller = auth()->user()->seller;
        $period = $request->get('period', '30'); // days

        // Overview stats
        $stats = $this->getOverviewStats($seller, $period);

        // Sales trend data
        $salesTrend = $this->getSalesTrend($seller, $period);

        // Top products
        $topProducts = $this->getTopProducts($seller, $period);

        // Traffic sources (products by views)
        $trafficData = $this->getTrafficData($seller, $period);

        // Revenue by license type
        $revenueByLicense = $this->getRevenueByLicense($seller, $period);

        // Recent transactions
        $recentTransactions = $this->getRecentTransactions($seller);

        return view('seller.analytics.index', compact(
            'seller',
            'stats',
            'salesTrend',
            'topProducts',
            'trafficData',
            'revenueByLicense',
            'recentTransactions',
            'period'
        ));
    }

    /**
     * Get overview statistics.
     * Optimized: Combines multiple queries into single aggregate queries
     */
    private function getOverviewStats($seller, $period): array
    {
        $startDate = Carbon::now()->subDays($period);
        $previousStart = Carbon::now()->subDays($period * 2);
        $previousEnd = Carbon::now()->subDays($period);

        // Optimized: Single query for current AND previous period stats
        $periodStats = $seller->orderItems()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->selectRaw("
                SUM(CASE WHEN order_items.created_at >= ? THEN seller_amount ELSE 0 END) as current_revenue,
                SUM(CASE WHEN order_items.created_at >= ? THEN 1 ELSE 0 END) as current_orders,
                SUM(CASE WHEN order_items.created_at >= ? AND order_items.created_at < ? THEN seller_amount ELSE 0 END) as previous_revenue,
                SUM(CASE WHEN order_items.created_at >= ? AND order_items.created_at < ? THEN 1 ELSE 0 END) as previous_orders
            ", [$startDate, $startDate, $previousStart, $previousEnd, $previousStart, $previousEnd])
            ->first();

        $currentRevenue = (float) ($periodStats->current_revenue ?? 0);
        $currentOrders = (int) ($periodStats->current_orders ?? 0);
        $previousRevenue = (float) ($periodStats->previous_revenue ?? 0);
        $previousOrders = (int) ($periodStats->previous_orders ?? 0);

        // Optimized: Single query for views and product count
        $productStats = $seller->products()
            ->selectRaw("
                SUM(views_count) as total_views,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_count
            ")
            ->first();

        $currentViews = (int) ($productStats->total_views ?? 0);
        $publishedCount = (int) ($productStats->published_count ?? 0);

        // Calculate percentage changes
        $revenueChange = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : ($currentRevenue > 0 ? 100 : 0);

        $ordersChange = $previousOrders > 0
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100
            : ($currentOrders > 0 ? 100 : 0);

        // Conversion rate (orders / views)
        $conversionRate = $currentViews > 0
            ? ($currentOrders / $currentViews) * 100
            : 0;

        // Average order value
        $avgOrderValue = $currentOrders > 0
            ? $currentRevenue / $currentOrders
            : 0;

        return [
            'revenue' => $currentRevenue,
            'revenue_change' => round($revenueChange, 1),
            'orders' => $currentOrders,
            'orders_change' => round($ordersChange, 1),
            'views' => $currentViews,
            'conversion_rate' => round($conversionRate, 2),
            'avg_order_value' => round($avgOrderValue, 2),
            'total_products' => $publishedCount,
            'total_earnings' => $seller->total_earnings ?? 0,
            'wallet_balance' => auth()->user()->getOrCreateWallet()->balance,
        ];
    }

    /**
     * Get sales trend data for chart.
     */
    private function getSalesTrend($seller, $period): array
    {
        $startDate = Carbon::now()->subDays($period);

        $sales = $seller->orderItems()
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(seller_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates
        $labels = [];
        $revenueData = [];
        $ordersData = [];

        $currentDate = $startDate->copy();
        $endDate = Carbon::now();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');

            $dayData = $sales->firstWhere('date', $dateStr);
            $revenueData[] = $dayData ? (float) $dayData->revenue : 0;
            $ordersData[] = $dayData ? (int) $dayData->orders : 0;

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'orders' => $ordersData,
        ];
    }

    /**
     * Get top performing products.
     */
    private function getTopProducts($seller, $period): array
    {
        $startDate = Carbon::now()->subDays($period);

        return $seller->products()
            ->withCount(['orderItems as sales_count' => function ($q) use ($startDate) {
                $q->whereHas('order', fn($o) => $o->where('status', 'completed'))
                    ->where('created_at', '>=', $startDate);
            }])
            ->withSum(['orderItems as revenue' => function ($q) use ($startDate) {
                $q->whereHas('order', fn($o) => $o->where('status', 'completed'))
                    ->where('created_at', '>=', $startDate);
            }], 'seller_amount')
            ->where('status', 'published')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'thumbnail' => $p->thumbnail_url ?? null,
                'sales' => $p->sales_count ?? 0,
                'revenue' => (float) ($p->revenue ?? 0),
                'views' => $p->views_count,
            ])
            ->toArray();
    }

    /**
     * Get traffic data (views by product).
     */
    private function getTrafficData($seller, $period): array
    {
        $products = $seller->products()
            ->where('status', 'published')
            ->orderByDesc('views_count')
            ->take(10)
            ->get(['id', 'name', 'views_count', 'sales_count']);

        $totalViews = $products->sum('views_count');

        return [
            'products' => $products->map(fn($p) => [
                'name' => \Illuminate\Support\Str::limit($p->name, 25),
                'views' => $p->views_count,
                'percentage' => $totalViews > 0 ? round(($p->views_count / $totalViews) * 100, 1) : 0,
                'sales' => $p->sales_count,
            ])->toArray(),
            'total_views' => $totalViews,
        ];
    }

    /**
     * Get revenue breakdown by license type.
     */
    private function getRevenueByLicense($seller, $period): array
    {
        $startDate = Carbon::now()->subDays($period);

        $revenue = $seller->orderItems()
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->where('created_at', '>=', $startDate)
            ->select(
                'license_type',
                DB::raw('SUM(seller_amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('license_type')
            ->get();

        $totalRevenue = $revenue->sum('revenue');

        return $revenue->map(fn($r) => [
            'type' => ucfirst($r->license_type ?? 'Standard'),
            'revenue' => (float) $r->revenue,
            'count' => $r->count,
            'percentage' => $totalRevenue > 0 ? round(($r->revenue / $totalRevenue) * 100, 1) : 0,
        ])->toArray();
    }

    /**
     * Get recent transactions.
     */
    private function getRecentTransactions($seller): \Illuminate\Support\Collection
    {
        return $seller->orderItems()
            ->with(['order.user', 'product'])
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * API: Get sales data for chart updates.
     */
    public function salesData(Request $request): JsonResponse
    {
        $seller = auth()->user()->seller;
        $period = $request->get('period', 30);

        return response()->json([
            'trend' => $this->getSalesTrend($seller, $period),
            'stats' => $this->getOverviewStats($seller, $period),
        ]);
    }

    /**
     * API: Get products data.
     */
    public function productsData(Request $request): JsonResponse
    {
        $seller = auth()->user()->seller;
        $period = $request->get('period', 30);

        return response()->json([
            'top_products' => $this->getTopProducts($seller, $period),
            'traffic' => $this->getTrafficData($seller, $period),
        ]);
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $seller = auth()->user()->seller;
        $period = $request->get('period', 30);
        $startDate = Carbon::now()->subDays($period);

        $data = $seller->orderItems()
            ->with(['order.user', 'product'])
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(fn($item) => [
                'Date' => $item->created_at->format('Y-m-d H:i'),
                'Product' => $item->product_name,
                'Customer' => $item->order->user->name ?? 'N/A',
                'License Type' => ucfirst($item->license_type ?? 'standard'),
                'Price' => format_price($item->price),
                'Your Earnings' => format_price($item->seller_amount),
                'Platform Fee' => format_price($item->platform_fee),
            ]);

        $filename = 'analytics-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
