<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Products';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $products = Product::orderBy('sales_count', 'desc')
            ->take(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $products->pluck('sales_count')->toArray(),
                    'backgroundColor' => [
                        'rgb(139, 92, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(192, 132, 252)',
                        'rgb(216, 180, 254)',
                        'rgb(233, 213, 255)',
                    ],
                ],
            ],
            'labels' => $products->pluck('name')->map(fn ($name) => strlen($name) > 15 ? substr($name, 0, 15) . '...' : $name)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
