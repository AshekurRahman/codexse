<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginActivity extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?User $user,
        public string $email,
        public string $ipAddress,
        public int $failedAttempts,
        public ?string $userAgent = null,
        public array $metadata = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('[Security Alert] Suspicious Login Activity Detected')
            ->error()
            ->greeting('Security Alert!')
            ->line("Suspicious login activity has been detected on your platform.")
            ->line("**Account:** {$this->email}")
            ->line("**Failed Attempts:** {$this->failedAttempts}")
            ->line("**IP Address:** {$this->ipAddress}")
            ->line("**Time:** " . now()->format('Y-m-d H:i:s T'));

        if ($this->userAgent) {
            $message->line("**User Agent:** {$this->userAgent}");
        }

        if ($this->user) {
            $message->action('View User Account', url("/admin/users/{$this->user->id}"));
        }

        return $message
            ->line('Please investigate this activity and take appropriate action if necessary.')
            ->salutation('Security System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'suspicious_login',
            'email' => $this->email,
            'user_id' => $this->user?->id,
            'ip_address' => $this->ipAddress,
            'failed_attempts' => $this->failedAttempts,
            'user_agent' => $this->userAgent,
            'metadata' => $this->metadata,
        ];
    }
}
