<?php

namespace App\Filament\Admin\Resources\GdprDataRequestResource\Pages;

use App\Filament\Admin\Resources\GdprDataRequestResource;
use App\Services\GdprService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewGdprDataRequest extends ViewRecord
{
    protected static string $resource = GdprDataRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('process_export')
                ->label('Process Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->type === 'export' && $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(GdprService::class)->processExportRequest($this->record);
                        Notification::make()
                            ->title('Export processed successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Export failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('process_deletion')
                ->label('Process Deletion')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn () => $this->record->type === 'deletion' && $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Process Account Deletion')
                ->modalDescription('This will permanently anonymize the user\'s data. This action cannot be undone!')
                ->action(function () {
                    try {
                        app(GdprService::class)->processDeletionRequest($this->record);
                        Notification::make()
                            ->title('Account deletion processed')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Deletion failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($this->record->status, ['pending', 'processing']))
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->reject($data['reason']);
                    Notification::make()
                        ->title('Request rejected')
                        ->success()
                        ->send();
                }),
        ];
    }
}
