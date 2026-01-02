<?php

namespace App\Notifications;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 60;

    protected Seller $seller;

    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seller Application Received - ' . config('app.name'))
            ->view('emails.seller.application-submitted', [
                'user' => $notifiable,
                'seller' => $this->seller,
                'recipientEmail' => $notifiable->email,
            ]);
    }
}
