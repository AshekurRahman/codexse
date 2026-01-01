<?php

namespace App\Filament\Admin\Resources\FraudAlertResource\Pages;

use App\Filament\Admin\Resources\FraudAlertResource;
use App\Models\FraudAlert;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFraudAlert extends ViewRecord
{
    protected static string $resource = FraudAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('start_review')
                ->label('Start Review')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->markAsReviewing();
                    Notification::make()
                        ->title('Alert is now under review.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('confirm_fraud')
                ->label('Confirm Fraud')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['pending', 'reviewing']))
                ->form([
                    \Filament\Forms\Components\Select::make('action')
                        ->label('Action to Take')
                        ->options(FraudAlert::ACTIONS)
                        ->default('blocked'),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->confirmFraud($data['action'], $data['notes']);
                    Notification::make()
                        ->title('Alert confirmed as fraud.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('false_positive')
                ->label('False Positive')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['pending', 'reviewing']))
                ->form([
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes'),
                ])
                ->action(function (array $data) {
                    $this->record->markAsFalsePositive($data['notes'] ?? null);
                    Notification::make()
                        ->title('Alert marked as false positive.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('block_ip')
                ->label('Block IP')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->ip_address !== null)
                ->form([
                    \Filament\Forms\Components\TextInput::make('hours')
                        ->label('Block Duration (hours)')
                        ->numeric()
                        ->placeholder('Leave empty for permanent'),
                    \Filament\Forms\Components\TextInput::make('reason')
                        ->default(fn () => 'Blocked due to fraud alert ' . $this->record->alert_number),
                ])
                ->action(function (array $data) {
                    app(\App\Services\FraudDetectionService::class)->blockIp(
                        $this->record->ip_address,
                        $data['reason'],
                        $data['hours'] ? (int) $data['hours'] : null
                    );
                    Notification::make()
                        ->title('IP address has been blocked.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
