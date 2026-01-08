<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class GeographicSalesMap extends Widget
{
    protected static string $view = 'filament.admin.widgets.geographic-sales-map';

    protected static ?int $sort = 14;

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

    public function getRegionData(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        // Get orders grouped by state/region
        $regionData = Order::whereIn('status', ['completed', 'processing', 'delivered'])
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('billing_state')
            ->select('billing_state', DB::raw('COUNT(*) as orders_count'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('billing_state')
            ->orderByDesc('total_revenue')
            ->get();

        // Get max revenue for intensity calculation
        $maxRevenue = $regionData->max('total_revenue') ?: 1;

        return $regionData->map(function ($region) use ($maxRevenue) {
            $intensity = min(100, round(($region->total_revenue / $maxRevenue) * 100));
            return [
                'region' => $region->billing_state,
                'orders' => $region->orders_count,
                'revenue' => $region->total_revenue,
                'intensity' => $intensity,
                'color' => $this->getIntensityColor($intensity),
            ];
        })->toArray();
    }

    public function getTopRegions(): array
    {
        return array_slice($this->getRegionData(), 0, 10);
    }

    public function getTotalStats(): array
    {
        $days = (int) $this->filter;
        $startDate = now()->subDays($days);

        $stats = Order::whereIn('status', ['completed', 'processing', 'delivered'])
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('COUNT(DISTINCT billing_state) as unique_regions')
            )
            ->first();

        return [
            'total_orders' => $stats->total_orders ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
            'unique_regions' => $stats->unique_regions ?? 0,
            'avg_per_region' => $stats->unique_regions > 0
                ? round($stats->total_revenue / $stats->unique_regions, 2)
                : 0,
        ];
    }

    protected function getIntensityColor(int $intensity): string
    {
        if ($intensity >= 80) {
            return 'bg-primary-600 dark:bg-primary-500';
        } elseif ($intensity >= 60) {
            return 'bg-primary-500 dark:bg-primary-400';
        } elseif ($intensity >= 40) {
            return 'bg-primary-400 dark:bg-primary-300';
        } elseif ($intensity >= 20) {
            return 'bg-primary-300 dark:bg-primary-200';
        }
        return 'bg-primary-200 dark:bg-primary-100';
    }

    public function getCountryDistribution(): array
    {
        // billing_country column not available - return empty array
        // Geographic distribution is shown by billing_state in getRegionData() instead
        return [];
    }
}
