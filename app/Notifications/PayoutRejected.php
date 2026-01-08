<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRejected extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Payout Request Rejected - $' . number_format($this->payout->amount, 2))
            ->greeting('Payout Request Update')
            ->line('Unfortunately, your payout request has been rejected.')
            ->line('**Payout Details:**')
            ->line('- **Amount:** $' . number_format($this->payout->amount, 2))
            ->line('- **Rejected On:** ' . $this->payout->rejected_at->format('F j, Y \a\t g:i A'))
            ->line('- **Reason:** ' . $this->payout->rejection_reason)
            ->line('The funds have been returned to your wallet balance.')
            ->action('View Wallet', route('wallet.index'))
            ->line('If you have questions about this decision, please contact support.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payout_rejected',
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'rejected_at' => $this->payout->rejected_at?->toISOString(),
            'reason' => $this->payout->rejection_reason,
            'message' => 'Payout rejected: $' . number_format($this->payout->amount, 2) . ' - ' . $this->payout->rejection_reason,
            'url' => route('wallet.index'),
        ];
    }
}
