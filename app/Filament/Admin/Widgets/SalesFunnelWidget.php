<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SalesFunnelWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.sales-funnel';

    protected static ?int $sort = 13;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    protected function getFilters(): array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
            '365' => 'Last year',
        ];
    }

    public function getFunnelData(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        // Total registered users in the period (as proxy for visitors)
        $totalUsers = User::where('created_at', '>=', $startDate)->count();

        // Use a multiplier to estimate visitors (typical conversion to registration is 2-5%)
        $estimatedVisitors = max($totalUsers * 20, Order::where('created_at', '>=', $startDate)->count() * 10);

        // Cart additions - estimate based on orders
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $cartAdditions = max($totalOrders, (int) ($totalOrders * 1.5));

        // Checkout initiated (all orders including cancelled)
        $checkoutsInitiated = $totalOrders;

        // Payment completed
        $paymentsCompleted = Order::whereIn('status', ['completed', 'processing', 'delivered'])
            ->where('created_at', '>=', $startDate)
            ->count();

        // Order delivered/fulfilled
        $ordersDelivered = Order::whereIn('status', ['completed', 'delivered'])
            ->where('created_at', '>=', $startDate)
            ->count();

        $funnel = [
            [
                'stage' => 'Visitors',
                'count' => $estimatedVisitors,
                'percent' => 100,
                'color' => 'bg-primary-500',
            ],
            [
                'stage' => 'Added to Cart',
                'count' => $cartAdditions,
                'percent' => $estimatedVisitors > 0 ? round(($cartAdditions / $estimatedVisitors) * 100, 1) : 0,
                'color' => 'bg-indigo-500',
            ],
            [
                'stage' => 'Checkout Started',
                'count' => $checkoutsInitiated,
                'percent' => $estimatedVisitors > 0 ? round(($checkoutsInitiated / $estimatedVisitors) * 100, 1) : 0,
                'color' => 'bg-violet-500',
            ],
            [
                'stage' => 'Payment Completed',
                'count' => $paymentsCompleted,
                'percent' => $estimatedVisitors > 0 ? round(($paymentsCompleted / $estimatedVisitors) * 100, 1) : 0,
                'color' => 'bg-purple-500',
            ],
            [
                'stage' => 'Order Fulfilled',
                'count' => $ordersDelivered,
                'percent' => $estimatedVisitors > 0 ? round(($ordersDelivered / $estimatedVisitors) * 100, 1) : 0,
                'color' => 'bg-fuchsia-500',
            ],
        ];

        // Calculate conversion rates between stages
        for ($i = 1; $i < count($funnel); $i++) {
            $funnel[$i]['conversion'] = $funnel[$i - 1]['count'] > 0
                ? round(($funnel[$i]['count'] / $funnel[$i - 1]['count']) * 100, 1)
                : 0;
        }

        return $funnel;
    }

    public function getOverallConversion(): float
    {
        $funnel = $this->getFunnelData();
        $first = $funnel[0]['count'];
        $last = end($funnel)['count'];

        return $first > 0 ? round(($last / $first) * 100, 2) : 0;
    }
}
