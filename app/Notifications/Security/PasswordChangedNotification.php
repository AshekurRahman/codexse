<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Password Was Changed')
            ->greeting("Hello {$this->user->name}!")
            ->line('Your account password was successfully changed.')
            ->line("**Time:** " . now()->format('Y-m-d H:i:s T'))
            ->line("**IP Address:** {$this->ipAddress}")
            ->line('If you made this change, you can safely ignore this email.')
            ->line('**If you did not change your password, your account may be compromised.**')
            ->line('Please take the following steps immediately:')
            ->line('1. Reset your password using the "Forgot Password" link')
            ->line('2. Enable two-factor authentication')
            ->line('3. Review your recent account activity')
            ->action('Reset Password', url('/forgot-password'))
            ->salutation('Stay secure!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_changed',
            'user_id' => $this->user->id,
            'ip_address' => $this->ipAddress,
        ];
    }
}
