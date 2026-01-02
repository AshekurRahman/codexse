<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('low_stock_alert');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Low Stock Products')
            ->description('Products running low on inventory')
            ->query(
                Product::query()
                    ->where('track_stock', true)
                    ->where('stock_quantity', '<=', 10)
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('stock_quantity', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('seller.store_name')
                    ->label('Seller')
                    ->limit(20),
            ])
            ->paginated(false)
            ->emptyStateHeading('All stocked up!')
            ->emptyStateDescription('No products are running low on stock.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
