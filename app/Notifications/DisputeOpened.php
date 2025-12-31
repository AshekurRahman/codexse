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

        if ($isInitiator) {
            return (new MailMessage)
                ->subject('Dispute Opened Successfully')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your dispute has been submitted successfully.')
                ->line('Reason: ' . ucfirst(str_replace('_', ' ', $this->dispute->reason)))
                ->line('Our team will review your case and get back to you soon.')
                ->action('View Dispute', url('/disputes/' . $this->dispute->id))
                ->line('You may be asked to provide additional information during the review process.');
        }

        return (new MailMessage)
            ->subject('Dispute Filed Against Your Transaction')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A dispute has been filed regarding a transaction.')
            ->line('Reason: ' . ucfirst(str_replace('_', ' ', $this->dispute->reason)))
            ->line('Please review the details and provide your response.')
            ->action('View Dispute', url('/disputes/' . $this->dispute->id))
            ->line('Our team will review both sides and make a fair decision.');
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
