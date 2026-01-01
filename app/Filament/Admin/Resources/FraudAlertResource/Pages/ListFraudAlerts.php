<?php

namespace App\Filament\Admin\Resources\FraudAlertResource\Pages;

use App\Filament\Admin\Resources\FraudAlertResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFraudAlerts extends ListRecords
{
    protected static string $resource = FraudAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('initialize_rules')
                ->label('Initialize Default Rules')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    \App\Models\FraudRule::initializeDefaults();
                    Notification::make()
                        ->title('Default fraud rules have been initialized.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Alerts'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(\App\Models\FraudAlert::where('status', 'pending')->count()),
            'high_risk' => Tab::make('High Risk')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('risk_score', '>=', 70))
                ->badge(\App\Models\FraudAlert::where('risk_score', '>=', 70)->where('status', 'pending')->count())
                ->badgeColor('danger'),
            'critical' => Tab::make('Critical')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('severity', 'critical'))
                ->badge(\App\Models\FraudAlert::where('severity', 'critical')->where('status', 'pending')->count())
                ->badgeColor('danger'),
            'blocked' => Tab::make('Auto Blocked')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('auto_blocked', true)),
            'resolved' => Tab::make('Resolved')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['confirmed_fraud', 'false_positive', 'resolved'])),
        ];
    }
}
