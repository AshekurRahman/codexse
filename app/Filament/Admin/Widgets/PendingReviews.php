<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingReviews extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('pending_reviews');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pending Product Reviews')
            ->description('Products awaiting approval')
            ->query(
                Product::query()
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
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
                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->label('Price'),
                Tables\Columns\TextColumn::make('seller.store_name')
                    ->label('Seller')
                    ->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Product $record) => route('filament.admin.resources.products.edit', $record)),
            ])
            ->paginated(false)
            ->emptyStateHeading('All caught up!')
            ->emptyStateDescription('No products pending review.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
