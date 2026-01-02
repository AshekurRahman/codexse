<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueForecastWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Forecast';

    protected static ?string $description = 'Actual revenue with 7-day prediction';

    protected static ?int $sort = 16;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '14' => 'Last 14 days',
            '30' => 'Last 30 days',
            '60' => 'Last 60 days',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $forecastDays = 7;
        $startDate = now()->subDays($days);

        // Get actual revenue data
        $actualData = Order::whereIn('status', ['completed', 'processing', 'delivered'])
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        // Fill in missing dates
        $labels = [];
        $actualRevenue = [];
        $forecastRevenue = [];
        $dataPoints = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $revenue = (float) ($actualData[$dateStr] ?? 0);

            $labels[] = $currentDate->format('M d');
            $actualRevenue[] = round($revenue, 2);
            $forecastRevenue[] = null; // Actual data, no forecast
            $dataPoints[] = ['x' => count($labels) - 1, 'y' => $revenue];

            $currentDate->addDay();
        }

        // Calculate forecast using linear regression
        $forecast = $this->calculateForecast($dataPoints, $forecastDays);

        // Add forecast data points
        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecastDate = now()->addDays($i);
            $labels[] = $forecastDate->format('M d');
            $actualRevenue[] = null; // No actual data for future
            $forecastRevenue[] = round($forecast[$i - 1] ?? 0, 2);
        }

        // Connect the lines by adding last actual value to forecast
        $lastActualIndex = count($actualRevenue) - $forecastDays - 1;
        if ($lastActualIndex >= 0 && $actualRevenue[$lastActualIndex] !== null) {
            $forecastRevenue[$lastActualIndex] = $actualRevenue[$lastActualIndex];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Actual Revenue',
                    'data' => $actualRevenue,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 3,
                    'pointBackgroundColor' => '#6366f1',
                ],
                [
                    'label' => 'Forecast',
                    'data' => $forecastRevenue,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                    'borderDash' => [5, 5],
                    'pointRadius' => 3,
                    'pointBackgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function calculateForecast(array $dataPoints, int $forecastDays): array
    {
        if (count($dataPoints) < 2) {
            return array_fill(0, $forecastDays, 0);
        }

        $n = count($dataPoints);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($dataPoints as $point) {
            $x = $point['x'];
            $y = $point['y'];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        // Linear regression: y = mx + b
        $denominator = ($n * $sumX2 - $sumX * $sumX);
        if ($denominator == 0) {
            $m = 0;
        } else {
            $m = ($n * $sumXY - $sumX * $sumY) / $denominator;
        }
        $b = ($sumY - $m * $sumX) / $n;

        // Generate forecast
        $forecast = [];
        $lastX = $n - 1;
        for ($i = 1; $i <= $forecastDays; $i++) {
            $predictedY = $m * ($lastX + $i) + $b;
            // Ensure no negative values
            $forecast[] = max(0, $predictedY);
        }

        return $forecast;
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
                    'callbacks' => [
                        'label' => 'function(context) {
                            if (context.raw === null) return null;
                            return context.dataset.label + ": $" + context.raw.toLocaleString();
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
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

    public function getForecastSummary(): array
    {
        $data = $this->getData();
        $actualData = array_filter($data['datasets'][0]['data'] ?? [], fn($v) => $v !== null);
        $forecastData = array_filter($data['datasets'][1]['data'] ?? [], fn($v) => $v !== null);

        $actualTotal = array_sum($actualData);
        $forecastTotal = array_sum($forecastData);
        $avgDaily = count($actualData) > 0 ? $actualTotal / count($actualData) : 0;

        return [
            'actual_total' => round($actualTotal, 2),
            'forecast_total' => round($forecastTotal, 2),
            'avg_daily' => round($avgDaily, 2),
            'forecast_trend' => $forecastTotal > ($avgDaily * 7) ? 'up' : 'down',
        ];
    }
}
