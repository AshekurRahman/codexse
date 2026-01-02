<?php

namespace App\Notifications;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 60;

    protected Seller $seller;
    protected ?string $reason;

    public function __construct(Seller $seller, ?string $reason = null)
    {
        $this->seller = $seller;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on Your Seller Application - ' . config('app.name'))
            ->view('emails.seller.application-rejected', [
                'user' => $notifiable,
                'seller' => $this->seller,
                'reason' => $this->reason,
                'recipientEmail' => $notifiable->email,
            ]);
    }
}
