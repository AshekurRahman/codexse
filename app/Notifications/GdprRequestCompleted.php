<?php

namespace App\Notifications;

use App\Models\GdprDataRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GdprRequestCompleted extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject("Your {$this->request->type_name} Request is Complete")
            ->greeting("Hello {$notifiable->name},");

        if ($this->request->type === GdprDataRequest::TYPE_EXPORT) {
            $mail->line("Your data export request (Reference: {$this->request->request_number}) has been completed.")
                ->line("You can now download your data. The download link will be available for 7 days.")
                ->action('Download Your Data', route('gdpr.download', $this->request))
                ->line("The download contains:")
                ->line("- Your personal information")
                ->line("- Order history and transactions")
                ->line("- Reviews and communications")
                ->line("- All other data we hold about you");
        } else {
            $mail->line("Your {$this->request->type_name} request (Reference: {$this->request->request_number}) has been completed.")
                ->action('View Request Details', route('gdpr.requests'));
        }

        return $mail->salutation("Best regards,\n" . config('app.name') . " Team");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gdpr_request_completed',
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'request_type' => $this->request->type,
            'message' => "Your {$this->request->type_name} request has been completed.",
            'download_available' => $this->request->type === GdprDataRequest::TYPE_EXPORT,
        ];
    }
}
