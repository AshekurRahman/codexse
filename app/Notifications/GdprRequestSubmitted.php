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
        return (new MailMessage)
            ->subject("Your {$this->request->type_name} Request Has Been Received")
            ->view('emails.gdpr.request-submitted', [
                'request' => $this->request,
                'user' => $notifiable,
                'recipientEmail' => $notifiable->email,
            ]);
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
