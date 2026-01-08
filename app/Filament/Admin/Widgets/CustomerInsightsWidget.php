<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class CustomerInsightsWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.customer-insights';

    protected static ?int $sort = 15;

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

    public function getCustomerMetrics(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        // New customers in period (users who made their first order)
        $newCustomers = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->select('user_id')
            ->distinct()
            ->whereNotExists(function ($query) use ($startDate) {
                $query->select(DB::raw(1))
                    ->from('orders as previous_orders')
                    ->whereColumn('previous_orders.user_id', 'orders.user_id')
                    ->where('previous_orders.created_at', '<', $startDate)
                    ->whereIn('previous_orders.status', ['completed', 'processing', 'delivered']);
            })
            ->count();

        // Returning customers (ordered before and during period)
        $returningCustomers = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->select('user_id')
            ->distinct()
            ->whereExists(function ($query) use ($startDate) {
                $query->select(DB::raw(1))
                    ->from('orders as previous_orders')
                    ->whereColumn('previous_orders.user_id', 'orders.user_id')
                    ->where('previous_orders.created_at', '<', $startDate)
                    ->whereIn('previous_orders.status', ['completed', 'processing', 'delivered']);
            })
            ->count();

        $totalCustomers = $newCustomers + $returningCustomers;

        // Average Lifetime Value
        $avgLTV = Order::whereIn('status', ['completed', 'processing', 'delivered'])
            ->select('user_id', DB::raw('SUM(total) as lifetime_value'))
            ->groupBy('user_id')
            ->get()
            ->avg('lifetime_value') ?? 0;

        // Average Order Value
        $avgOrderValue = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->avg('total') ?? 0;

        // Repeat purchase rate
        $repeatPurchaseRate = $totalCustomers > 0
            ? round(($returningCustomers / $totalCustomers) * 100, 1)
            : 0;

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'total_customers' => $totalCustomers,
            'new_percentage' => $totalCustomers > 0 ? round(($newCustomers / $totalCustomers) * 100, 1) : 0,
            'returning_percentage' => $totalCustomers > 0 ? round(($returningCustomers / $totalCustomers) * 100, 1) : 0,
            'avg_ltv' => round($avgLTV, 2),
            'avg_order_value' => round($avgOrderValue, 2),
            'repeat_purchase_rate' => $repeatPurchaseRate,
        ];
    }

    public function getTopCustomers(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        return Order::where('orders.created_at', '>=', $startDate)
            ->whereIn('orders.status', ['completed', 'processing', 'delivered'])
            ->whereNotNull('orders.user_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.social_avatar',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.social_avatar')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'avatar' => $customer->social_avatar,
                'orders_count' => $customer->orders_count,
                'total_spent' => $customer->total_spent,
            ])
            ->toArray();
    }

    public function getCustomerGrowth(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        // Get daily new customer counts
        $dailyData = User::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill in missing dates
        $result = [];
        $currentDate = $startDate->copy();
        $cumulative = User::where('created_at', '<', $startDate)->count();

        while ($currentDate <= now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $dailyCount = $dailyData[$dateStr] ?? 0;
            $cumulative += $dailyCount;

            $result[] = [
                'date' => $currentDate->format('M d'),
                'new' => $dailyCount,
                'cumulative' => $cumulative,
            ];

            $currentDate->addDay();
        }

        // Sample every Nth element if too many data points
        if (count($result) > 30) {
            $step = ceil(count($result) / 30);
            $sampled = [];
            for ($i = 0; $i < count($result); $i += $step) {
                $sampled[] = $result[$i];
            }
            return $sampled;
        }

        return $result;
    }

    public function getCustomerSegments(): array
    {
        // Segment customers by order frequency
        $segments = [];

        // One-time buyers
        $oneTime = DB::table('orders')
            ->select('user_id')
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        // Occasional (2-3 orders)
        $occasional = DB::table('orders')
            ->select('user_id')
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) BETWEEN 2 AND 3')
            ->count();

        // Regular (4-10 orders)
        $regular = DB::table('orders')
            ->select('user_id')
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) BETWEEN 4 AND 10')
            ->count();

        // VIP (10+ orders)
        $vip = DB::table('orders')
            ->select('user_id')
            ->whereIn('status', ['completed', 'processing', 'delivered'])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 10')
            ->count();

        $total = $oneTime + $occasional + $regular + $vip;

        return [
            [
                'segment' => 'One-time',
                'count' => $oneTime,
                'percentage' => $total > 0 ? round(($oneTime / $total) * 100, 1) : 0,
                'color' => 'bg-gray-400',
            ],
            [
                'segment' => 'Occasional',
                'count' => $occasional,
                'percentage' => $total > 0 ? round(($occasional / $total) * 100, 1) : 0,
                'color' => 'bg-blue-400',
            ],
            [
                'segment' => 'Regular',
                'count' => $regular,
                'percentage' => $total > 0 ? round(($regular / $total) * 100, 1) : 0,
                'color' => 'bg-green-400',
            ],
            [
                'segment' => 'VIP',
                'count' => $vip,
                'percentage' => $total > 0 ? round(($vip / $total) * 100, 1) : 0,
                'color' => 'bg-purple-400',
            ],
        ];
    }
}
