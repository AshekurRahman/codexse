<?php

namespace App\Notifications;

use App\Models\FraudAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FraudAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FraudAlert $alert
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->alert->severity_name}] Fraud Alert: {$this->alert->type_name}")
            ->view('emails.admin.fraud-alert', [
                'alert' => $this->alert,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fraud_alert',
            'alert_id' => $this->alert->id,
            'alert_number' => $this->alert->alert_number,
            'alert_type' => $this->alert->type,
            'severity' => $this->alert->severity,
            'risk_score' => $this->alert->risk_score,
            'amount' => $this->alert->transaction_amount,
            'user_id' => $this->alert->user_id,
            'auto_blocked' => $this->alert->auto_blocked,
            'message' => "Fraud alert: {$this->alert->type_name} ({$this->alert->severity_name})",
        ];
    }
}
