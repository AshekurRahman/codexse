<?php

namespace App\Filament\Admin\Resources\EmailCampaignResource\Pages;

use App\Filament\Admin\Resources\EmailCampaignResource;
use App\Jobs\ProcessEmailCampaignJob;
use App\Mail\CampaignMail;
use App\Models\NewsletterSubscriber;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewEmailCampaign extends ViewRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->isDraft() || $this->record->isIdle()),

            Actions\Action::make('sendTest')
                ->label('Send Test Email')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->form([
                    Forms\Components\TextInput::make('test_email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->default(fn () => auth()->user()->email)
                        ->helperText('Send a test email to preview this campaign.'),
                ])
                ->action(function (array $data) {
                    try {
                        $testSubscriber = new NewsletterSubscriber([
                            'id' => 0,
                            'email' => $data['test_email'],
                            'token' => 'test-' . uniqid(),
                        ]);

                        Mail::to($data['test_email'])
                            ->send(new CampaignMail($this->record, $testSubscriber));

                        Notification::make()
                            ->title('Test email sent')
                            ->body("A test email has been sent to {$data['test_email']}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to send test email')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('start')
                ->label('Start Campaign')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => ($this->record->isDraft() || $this->record->isIdle()) && !$this->record->isRunning())
                ->requiresConfirmation()
                ->modalHeading('Start Campaign')
                ->modalDescription(fn () => "Start sending \"{$this->record->name}\" to subscribers? Daily limit: {$this->record->daily_limit} emails/day for {$this->record->sending_duration_days} days.")
                ->modalSubmitActionLabel('Start Sending')
                ->action(function () {
                    $subscriberCount = NewsletterSubscriber::active()->count();

                    if ($subscriberCount === 0) {
                        Notification::make()
                            ->title('No active subscribers')
                            ->body('There are no active newsletter subscribers to send to.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $this->record->start();
                    dispatch(new ProcessEmailCampaignJob($this->record));

                    Notification::make()
                        ->title('Campaign started')
                        ->body("Sending to {$subscriberCount} subscribers. Daily limit: {$this->record->daily_limit}")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('pause')
                ->label('Pause')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->visible(fn () => $this->record->isRunning())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->pause();

                    Notification::make()
                        ->title('Campaign paused')
                        ->body('You can resume sending at any time.')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('resume')
                ->label('Resume')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => $this->record->isPaused())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->resume();
                    dispatch(new ProcessEmailCampaignJob($this->record));

                    Notification::make()
                        ->title('Campaign resumed')
                        ->body('Sending will continue.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('stop')
                ->label('Stop')
                ->icon('heroicon-o-stop')
                ->color('danger')
                ->visible(fn () => $this->record->isRunning() || $this->record->isPaused())
                ->requiresConfirmation()
                ->modalHeading('Stop Campaign')
                ->modalDescription('Are you sure you want to stop this campaign? This action cannot be undone.')
                ->modalSubmitActionLabel('Stop Campaign')
                ->action(function () {
                    $this->record->stop();

                    Notification::make()
                        ->title('Campaign stopped')
                        ->body('The campaign has been permanently stopped.')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
