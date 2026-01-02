<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ConversionRateTracker extends ChartWidget
{
    protected static ?string $heading = 'Conversion Rate Tracker';

    protected static ?string $description = 'Daily order completion rate';

    protected static ?int $sort = 17;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    public ?string $filter = '14';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '14' => 'Last 14 days',
            '30' => 'Last 30 days',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        // Get daily conversion rates
        $dailyData = Order::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status IN ("completed", "delivered") THEN 1 ELSE 0 END) as completed_orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                $rate = $item->total_orders > 0
                    ? round(($item->completed_orders / $item->total_orders) * 100, 1)
                    : 0;
                return [$item->date => [
                    'total' => $item->total_orders,
                    'completed' => $item->completed_orders,
                    'rate' => $rate,
                ]];
            })
            ->toArray();

        // Fill in missing dates
        $labels = [];
        $conversionRates = [];
        $totalOrders = [];
        $completedOrders = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $data = $dailyData[$dateStr] ?? ['total' => 0, 'completed' => 0, 'rate' => 0];

            $labels[] = $currentDate->format('M d');
            $conversionRates[] = $data['rate'];
            $totalOrders[] = $data['total'];
            $completedOrders[] = $data['completed'];

            $currentDate->addDay();
        }

        // Calculate average conversion rate for benchmark line
        $avgRate = count($conversionRates) > 0 ? array_sum($conversionRates) / count($conversionRates) : 0;
        $benchmarkLine = array_fill(0, count($labels), round($avgRate, 1));

        return [
            'datasets' => [
                [
                    'label' => 'Conversion Rate %',
                    'data' => $conversionRates,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#10b981',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Average',
                    'data' => $benchmarkLine,
                    'borderColor' => '#6b7280',
                    'borderDash' => [5, 5],
                    'pointRadius' => 0,
                    'fill' => false,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Orders',
                    'data' => $totalOrders,
                    'type' => 'bar',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)',
                    'borderColor' => '#6366f1',
                    'borderWidth' => 1,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'max' => 100,
                    'title' => [
                        'display' => true,
                        'text' => 'Conversion %',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Orders',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getConversionStats(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        $stats = Order::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status IN ("completed", "delivered") THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->first();

        $total = $stats->total_orders ?? 0;
        $completed = $stats->completed ?? 0;

        return [
            'total_orders' => $total,
            'completed' => $completed,
            'cancelled' => $stats->cancelled ?? 0,
            'pending' => $stats->pending ?? 0,
            'conversion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }
}
