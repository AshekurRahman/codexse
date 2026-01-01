<?php

namespace App\Filament\Admin\Resources\GdprDataRequestResource\Pages;

use App\Filament\Admin\Resources\GdprDataRequestResource;
use App\Services\GdprService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGdprDataRequests extends ListRecords
{
    protected static string $resource = GdprDataRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cleanup_exports')
                ->label('Cleanup Expired Exports')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $count = app(GdprService::class)->cleanupExpiredExports();
                    Notification::make()
                        ->title("{$count} expired export(s) cleaned up")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(\App\Models\GdprDataRequest::where('status', 'pending')->count()),
            'export' => Tab::make('Export Requests')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'export')),
            'deletion' => Tab::make('Deletion Requests')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'deletion'))
                ->badgeColor('danger'),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
        ];
    }
}
