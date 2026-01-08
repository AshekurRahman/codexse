<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorDisabledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $ipAddress,
        public ?string $userAgent = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Security Notice] Two-Factor Authentication Disabled')
            ->greeting("Hello {$this->user->name}!")
            ->line('Two-factor authentication has been disabled on your account.')
            ->line("**Time:** " . now()->format('Y-m-d H:i:s T'))
            ->line("**IP Address:** {$this->ipAddress}")
            ->line('Your account is now less secure. We strongly recommend keeping two-factor authentication enabled.')
            ->line('If you did not make this change, please secure your account immediately.')
            ->action('Re-enable 2FA', url('/profile/security'))
            ->salutation('Stay secure!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'two_factor_disabled',
            'user_id' => $this->user->id,
            'ip_address' => $this->ipAddress,
        ];
    }
}
