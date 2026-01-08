<?php

namespace App\Notifications\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $ipAddress,
        public ?string $userAgent = null,
        public ?string $location = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('New Login to Your Account')
            ->greeting("Hello {$this->user->name}!")
            ->line('We detected a new login to your account.')
            ->line("**Time:** " . now()->format('Y-m-d H:i:s T'))
            ->line("**IP Address:** {$this->ipAddress}");

        if ($this->location) {
            $message->line("**Location:** {$this->location}");
        }

        if ($this->userAgent) {
            $message->line("**Device:** " . $this->parseUserAgent());
        }

        return $message
            ->line('If this was you, you can safely ignore this email.')
            ->line('If you did not login, please secure your account immediately by changing your password and enabling two-factor authentication.')
            ->action('Secure Your Account', url('/profile/security'))
            ->salutation('Stay secure!');
    }

    protected function parseUserAgent(): string
    {
        if (!$this->userAgent) {
            return 'Unknown Device';
        }

        // Simple parsing - in production you might use a proper user agent parser
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        if (str_contains($this->userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($this->userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($this->userAgent, 'Safari')) {
            $browser = 'Safari';
        } elseif (str_contains($this->userAgent, 'Edge')) {
            $browser = 'Edge';
        }

        if (str_contains($this->userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($this->userAgent, 'Mac')) {
            $os = 'macOS';
        } elseif (str_contains($this->userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($this->userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($this->userAgent, 'iPhone') || str_contains($this->userAgent, 'iPad')) {
            $os = 'iOS';
        }

        return "{$browser} on {$os}";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_device_login',
            'user_id' => $this->user->id,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
        ];
    }
}
