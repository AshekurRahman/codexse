<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Seller;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SellersChart extends ChartWidget
{
    protected static ?string $heading = 'New Sellers';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = collect();
        $labels = collect();

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels->push($month->format('M'));

            $count = Seller::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $data->push($count);
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Sellers',
                    'data' => $data->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
