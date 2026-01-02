<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('revenue_chart');
    }

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
            '365' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;

        $data = collect();
        $labels = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels->push($date->format($days > 30 ? 'M d' : 'D'));

            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total');

            $data->push($revenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->toArray(),
                    'fill' => true,
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'borderColor' => 'rgb(139, 92, 246)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels->toArray(),
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '$' + value; }",
                    ],
                ],
            ],
        ];
    }
}
