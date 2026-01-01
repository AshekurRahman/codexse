<?php

namespace App\Notifications;

use App\Models\EscrowTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscrowHeld extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EscrowTransaction $escrow,
        public bool $isBuyer = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Secured in Escrow - $' . number_format($this->escrow->amount, 2))
            ->view('emails.escrow.payment-held', [
                'escrow' => $this->escrow,
                'recipient' => $notifiable,
                'isBuyer' => $this->isBuyer,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'escrow_held',
            'escrow_id' => $this->escrow->id,
            'transaction_number' => $this->escrow->transaction_number,
            'amount' => $this->escrow->amount,
            'message' => 'Payment secured in escrow: $' . number_format($this->escrow->amount, 2),
            'url' => '/escrow/' . $this->escrow->id,
        ];
    }
}
