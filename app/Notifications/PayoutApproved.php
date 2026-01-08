<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payout $payout
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Payout Approved - $' . number_format($this->payout->amount, 2))
            ->greeting('Good news!')
            ->line('Your payout request has been approved.')
            ->line('**Payout Details:**')
            ->line('- **Amount:** $' . number_format($this->payout->amount, 2))
            ->line('- **Approved On:** ' . $this->payout->approved_at->format('F j, Y \a\t g:i A'));

        if ($this->payout->approval_notes) {
            $message->line('- **Notes:** ' . $this->payout->approval_notes);
        }

        return $message
            ->line('Your payout will now be processed and sent to your connected payment account.')
            ->action('View Payouts', route('seller.payouts.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payout_approved',
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'approved_at' => $this->payout->approved_at?->toISOString(),
            'message' => 'Payout approved: $' . number_format($this->payout->amount, 2),
            'url' => route('seller.payouts.index'),
        ];
    }
}
