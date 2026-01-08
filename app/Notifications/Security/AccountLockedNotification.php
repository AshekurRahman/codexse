<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountLockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $ipAddress,
        public int $failedAttempts,
        public \DateTimeInterface $lockedUntil,
        public ?string $userAgent = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Security Alert] Account Locked Due to Failed Login Attempts')
            ->error()
            ->greeting('Account Locked!')
            ->line("An account has been automatically locked due to multiple failed login attempts.")
            ->line("**Account:** {$this->user->email}")
            ->line("**User ID:** {$this->user->id}")
            ->line("**Failed Attempts:** {$this->failedAttempts}")
            ->line("**Locked Until:** " . $this->lockedUntil->format('Y-m-d H:i:s T'))
            ->line("**IP Address:** {$this->ipAddress}")
            ->line("**User Agent:** " . ($this->userAgent ?? 'Unknown'))
            ->action('View User Account', url("/admin/users/{$this->user->id}"))
            ->line('This could indicate a brute force attack. Please review and take appropriate action.')
            ->salutation('Security System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account_locked',
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'ip_address' => $this->ipAddress,
            'failed_attempts' => $this->failedAttempts,
            'locked_until' => $this->lockedUntil->format('Y-m-d H:i:s'),
        ];
    }
}
