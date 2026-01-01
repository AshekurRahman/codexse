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
        $severityColors = [
            'low' => '#3B82F6',
            'medium' => '#F59E0B',
            'high' => '#EF4444',
            'critical' => '#DC2626',
        ];

        return (new MailMessage)
            ->subject("[{$this->alert->severity_name}] Fraud Alert: {$this->alert->type_name}")
            ->greeting("Fraud Alert Detected")
            ->line("A suspicious transaction has been flagged by our fraud detection system.")
            ->line("**Alert Number:** {$this->alert->alert_number}")
            ->line("**Type:** {$this->alert->type_name}")
            ->line("**Severity:** {$this->alert->severity_name}")
            ->line("**Risk Score:** {$this->alert->risk_score}/100")
            ->line("**Amount:** {$this->alert->formatted_amount}")
            ->line("**User:** " . ($this->alert->user?->name ?? 'Guest'))
            ->line("**IP Address:** {$this->alert->ip_address}")
            ->when($this->alert->auto_blocked, function ($message) {
                return $message->line("**Status:** Transaction was automatically blocked.");
            })
            ->action('Review Alert', url("/admin/fraud-alerts/{$this->alert->id}"))
            ->line("Please review this alert and take appropriate action.");
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
