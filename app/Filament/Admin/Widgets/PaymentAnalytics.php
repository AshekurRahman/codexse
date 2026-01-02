<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Refund;
use App\Models\WalletTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentAnalytics extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Today's revenue
        $todayRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $today)
            ->sum('total');

        // This month's revenue
        $thisMonthRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total');

        // Last month's revenue for comparison
        $lastMonthRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('total');

        // Revenue change percentage
        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 100;

        // Payment method breakdown
        $paymentMethods = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        $stripeTotal = $paymentMethods->where('payment_method', 'stripe')->first()?->total ?? 0;
        $paypalTotal = $paymentMethods->where('payment_method', 'paypal')->first()?->total ?? 0;
        $walletTotal = $paymentMethods->where('payment_method', 'wallet')->first()?->total ?? 0;

        // Pending refunds
        $pendingRefunds = Refund::where('status', 'pending')->count();
        $pendingRefundAmount = Refund::where('status', 'pending')->sum('amount');

        // Completed refunds this month
        $completedRefunds = Refund::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('amount');

        // Failed payments
        $failedOrders = Order::where('status', 'failed')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Conversion rate (completed / total orders)
        $totalOrders = Order::where('created_at', '>=', $thisMonth)->count();
        $completedOrders = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        return [
            Stat::make('Today\'s Revenue', '$' . number_format($todayRevenue, 2))
                ->description('Completed orders today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Monthly Revenue', '$' . number_format($thisMonthRevenue, 2))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% from last month" : "{$revenueChange}% from last month")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChartData()),

            Stat::make('Conversion Rate', $conversionRate . '%')
                ->description("{$completedOrders} of {$totalOrders} orders")
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($conversionRate >= 70 ? 'success' : ($conversionRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Pending Refunds', $pendingRefunds)
                ->description('$' . number_format($pendingRefundAmount, 2) . ' total')
                ->descriptionIcon('heroicon-m-receipt-refund')
                ->color($pendingRefunds > 0 ? 'warning' : 'success'),

            Stat::make('Stripe Revenue', '$' . number_format($stripeTotal, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Wallet Payments', '$' . number_format($walletTotal, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('info'),
        ];
    }

    protected function getRevenueChartData(): array
    {
        return Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->map(fn ($value) => (float) $value)
            ->toArray();
    }
}
