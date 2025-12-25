<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Status';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelled = Order::where('status', 'cancelled')->count();
        $refunded = Order::where('status', 'refunded')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [$pending, $processing, $completed, $cancelled, $refunded],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)',  // warning - pending
                        'rgb(14, 165, 233)',  // info - processing
                        'rgb(34, 197, 94)',   // success - completed
                        'rgb(239, 68, 68)',   // danger - cancelled
                        'rgb(156, 163, 175)', // gray - refunded
                    ],
                ],
            ],
            'labels' => ['Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
