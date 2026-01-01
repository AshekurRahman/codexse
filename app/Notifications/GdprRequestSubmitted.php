<?php

namespace App\Notifications;

use App\Models\GdprDataRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GdprRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public GdprDataRequest $request
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeName = $this->request->type_name;

        return (new MailMessage)
            ->subject("Your {$typeName} Request Has Been Received")
            ->greeting("Hello {$notifiable->name},")
            ->line("We have received your {$typeName} request (Reference: {$this->request->request_number}).")
            ->line("Under GDPR regulations, we are required to process your request within 30 days.")
            ->line("What happens next:")
            ->line("1. Our team will verify your identity")
            ->line("2. We will process your request")
            ->line("3. You will receive a notification when it's complete")
            ->action('View Request Status', route('gdpr.requests'))
            ->line("If you did not make this request, please contact our support team immediately.")
            ->salutation("Best regards,\n" . config('app.name') . " Team");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gdpr_request_submitted',
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'request_type' => $this->request->type,
            'message' => "Your {$this->request->type_name} request has been submitted.",
        ];
    }
}
