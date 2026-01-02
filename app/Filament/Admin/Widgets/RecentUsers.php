<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUsers extends BaseWidget
{
    protected static ?int $sort = 9;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('recent_users');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Users')
            ->description('Newly registered users')
            ->query(
                User::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->size(32),
                Tables\Columns\TextColumn::make('name')
                    ->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->size('xs'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No users yet')
            ->emptyStateIcon('heroicon-o-users');
    }
}
