<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Dispute $dispute
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Dispute Resolved - #' . $this->dispute->id)
            ->view('emails.dispute.resolved', [
                'dispute' => $this->dispute,
                'recipient' => $notifiable,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'dispute_resolved',
            'dispute_id' => $this->dispute->id,
            'resolution' => $this->dispute->resolution,
            'message' => 'Dispute resolved: ' . ucfirst($this->dispute->resolution ?? 'Completed'),
            'url' => '/disputes/' . $this->dispute->id,
        ];
    }
}
