<?php

namespace App\Notifications;

use App\Models\EscrowTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscrowReleased extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EscrowTransaction $transaction
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Released - $' . number_format($this->transaction->payee_amount, 2))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Payment has been released to your account.')
            ->line('Transaction: ' . $this->transaction->transaction_number)
            ->line('Amount: $' . number_format($this->transaction->payee_amount, 2))
            ->action('View Balance', url('/seller/wallet'))
            ->line('The funds are now available in your seller balance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'escrow_released',
            'transaction_id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'amount' => $this->transaction->payee_amount,
            'message' => 'Payment released: $' . number_format($this->transaction->payee_amount, 2),
            'url' => '/seller/wallet',
        ];
    }
}
