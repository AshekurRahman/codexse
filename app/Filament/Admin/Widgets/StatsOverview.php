<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        $totalProducts = Product::count();
        $pendingProducts = Product::where('status', 'pending')->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('$' . number_format($monthlyRevenue, 2) . ' this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Total Orders', $totalOrders)
                ->description($pendingOrders . ' pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->chart([3, 5, 7, 4, 6, 8, 5, 7]),

            Stat::make('Total Users', $totalUsers)
                ->description($newUsersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->chart([2, 4, 3, 5, 4, 6, 5, 7]),

            Stat::make('Products', $totalProducts)
                ->description($pendingProducts . ' pending review')
                ->descriptionIcon('heroicon-m-cube')
                ->color($pendingProducts > 0 ? 'warning' : 'primary')
                ->chart([5, 3, 6, 4, 7, 5, 8, 6]),
        ];
    }
}
