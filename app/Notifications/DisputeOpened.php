<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeOpened extends Notification implements ShouldQueue
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
        $isInitiator = $notifiable->id === $this->dispute->initiated_by;

        return (new MailMessage)
            ->subject($isInitiator ? 'Dispute Opened Successfully' : 'Dispute Filed Against Your Transaction')
            ->view('emails.dispute.opened', [
                'dispute' => $this->dispute,
                'recipient' => $notifiable,
                'isInitiator' => $isInitiator,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'dispute_opened',
            'dispute_id' => $this->dispute->id,
            'reason' => $this->dispute->reason,
            'message' => 'Dispute opened: ' . ucfirst(str_replace('_', ' ', $this->dispute->reason)),
            'url' => '/disputes/' . $this->dispute->id,
        ];
    }
}
