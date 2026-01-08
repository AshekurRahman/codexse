<?php

namespace App\Notifications\Security;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminSecurityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    public function __construct(
        public string $alertType,
        public string $title,
        public string $description,
        public string $severity = self::SEVERITY_MEDIUM,
        public array $metadata = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $severityLabel = strtoupper($this->severity);
        $subject = "[{$severityLabel}] Security Alert: {$this->title}";

        $message = (new MailMessage)
            ->subject($subject);

        // Set color based on severity
        if (in_array($this->severity, [self::SEVERITY_HIGH, self::SEVERITY_CRITICAL])) {
            $message->error();
        }

        $message
            ->greeting("Security Alert - {$severityLabel}")
            ->line("**Alert Type:** {$this->alertType}")
            ->line("**Severity:** {$severityLabel}")
            ->line("**Time:** " . now()->format('Y-m-d H:i:s T'))
            ->line('')
            ->line($this->description);

        // Add metadata as details
        if (!empty($this->metadata)) {
            $message->line('');
            $message->line('**Additional Details:**');
            foreach ($this->metadata as $key => $value) {
                $formattedKey = ucfirst(str_replace('_', ' ', $key));
                $formattedValue = is_array($value) ? json_encode($value) : $value;
                $message->line("- {$formattedKey}: {$formattedValue}");
            }
        }

        return $message
            ->action('View Security Dashboard', url('/admin/security-dashboard'))
            ->line('Please review this alert and take appropriate action.')
            ->salutation('Security System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_security_alert',
            'alert_type' => $this->alertType,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'metadata' => $this->metadata,
        ];
    }
}
