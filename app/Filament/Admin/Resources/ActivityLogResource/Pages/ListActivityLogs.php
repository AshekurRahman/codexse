<?php

namespace App\Filament\Admin\Resources\ActivityLogResource\Pages;

use App\Filament\Admin\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $logs = ActivityLog::with('user')
                        ->latest()
                        ->limit(1000)
                        ->get();

                    $csv = "Time,User,Action,Category,Description,IP Address,Device\n";
                    foreach ($logs as $log) {
                        $csv .= sprintf(
                            "%s,%s,%s,%s,%s,%s,%s\n",
                            $log->created_at->format('Y-m-d H:i:s'),
                            $log->user?->name ?? 'Guest',
                            $log->action_name,
                            $log->category_name,
                            '"' . str_replace('"', '""', $log->description) . '"',
                            $log->ip_address,
                            '"' . str_replace('"', '""', $log->device_info) . '"'
                        );
                    }

                    return response()->streamDownload(function () use ($csv) {
                        echo $csv;
                    }, 'activity-logs-' . now()->format('Y-m-d') . '.csv');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(ActivityLog::count()),
            'auth' => Tab::make('Authentication')
                ->badge(ActivityLog::forCategory(ActivityLog::CATEGORY_AUTH)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->forCategory(ActivityLog::CATEGORY_AUTH)),
            'security' => Tab::make('Security')
                ->badge(ActivityLog::forCategory(ActivityLog::CATEGORY_SECURITY)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->forCategory(ActivityLog::CATEGORY_SECURITY)),
            'suspicious' => Tab::make('Suspicious')
                ->badge(ActivityLog::suspicious()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->suspicious()),
            'orders' => Tab::make('Orders')
                ->badge(ActivityLog::forCategory(ActivityLog::CATEGORY_ORDER)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->forCategory(ActivityLog::CATEGORY_ORDER)),
            'payments' => Tab::make('Payments')
                ->badge(ActivityLog::forCategory(ActivityLog::CATEGORY_PAYMENT)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->forCategory(ActivityLog::CATEGORY_PAYMENT)),
        ];
    }
}
