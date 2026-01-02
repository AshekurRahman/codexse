<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\DashboardSettings;
use App\Models\ActivityLog as ActivityLogModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActivityLog extends BaseWidget
{
    protected static ?int $sort = 12;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return DashboardSettings::isWidgetEnabled('activity_log');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Activity')
            ->description('Latest admin actions')
            ->query(
                ActivityLogModel::query()
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('user.avatar_url')
                    ->label('')
                    ->circular()
                    ->size(28),
                Tables\Columns\TextColumn::make('description')
                    ->limit(35)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->size('xs'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No activity yet')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
