<?php

namespace App\Filament\Admin\Resources\SellerVerificationResource\Pages;

use App\Filament\Admin\Resources\SellerVerificationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSellerVerification extends ViewRecord
{
    protected static string $resource = SellerVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Verification')
                ->form([
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->label('Notes (optional)'),
                    \Filament\Forms\Components\DatePicker::make('expires_at')
                        ->label('Verification Expires At (optional)')
                        ->helperText('Leave empty for permanent verification'),
                ])
                ->visible(fn () => in_array($this->record->status, ['pending', 'under_review']))
                ->action(function (array $data) {
                    $expiresAt = $data['expires_at'] ? new \DateTime($data['expires_at']) : null;
                    $this->record->approve(auth()->user(), $data['admin_notes'], $expiresAt);
                    Notification::make()
                        ->title('Verification approved successfully')
                        ->success()
                        ->send();
                    $this->redirect(SellerVerificationResource::getUrl('index'));
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Verification')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->label('Internal Notes (optional)'),
                ])
                ->visible(fn () => in_array($this->record->status, ['pending', 'under_review']))
                ->action(function (array $data) {
                    $this->record->reject(auth()->user(), $data['rejection_reason'], $data['admin_notes']);
                    Notification::make()
                        ->title('Verification rejected')
                        ->warning()
                        ->send();
                    $this->redirect(SellerVerificationResource::getUrl('index'));
                }),
        ];
    }
}
