<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutApprovalRequest extends Notification implements ShouldQueue
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
        $seller = $this->payout->seller;
        $sellerUser = $seller?->user;

        return (new MailMessage)
            ->subject('Payout Approval Required - $' . number_format($this->payout->amount, 2))
            ->greeting('Payout Approval Required')
            ->line('A large payout request requires your approval.')
            ->line('**Payout Details:**')
            ->line('- **Amount:** $' . number_format($this->payout->amount, 2))
            ->line('- **Seller:** ' . ($seller?->business_name ?? 'Unknown'))
            ->line('- **Email:** ' . ($sellerUser?->email ?? 'Unknown'))
            ->line('- **Request Date:** ' . $this->payout->created_at->format('F j, Y \a\t g:i A'))
            ->line('This payout exceeds the $' . number_format(Payout::APPROVAL_THRESHOLD, 2) . ' threshold and requires admin approval before processing.')
            ->action('Review Payout', url('/admin/payouts/' . $this->payout->id . '/edit'))
            ->line('Please review and approve or reject this payout request.');
    }

    public function toArray(object $notifiable): array
    {
        $seller = $this->payout->seller;

        return [
            'type' => 'payout_approval_request',
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'seller_id' => $seller?->id,
            'seller_name' => $seller?->business_name,
            'message' => 'Payout approval required: $' . number_format($this->payout->amount, 2) . ' for ' . ($seller?->business_name ?? 'Unknown'),
            'url' => '/admin/payouts/' . $this->payout->id . '/edit',
        ];
    }
}
