<?php

namespace App\Notifications;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSellerApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 60;

    protected Seller $seller;
    protected User $applicant;

    public function __construct(Seller $seller, User $applicant)
    {
        $this->seller = $seller;
        $this->applicant = $applicant;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Seller Application - ' . $this->seller->store_name)
            ->view('emails.admin.new-seller-application', [
                'admin' => $notifiable,
                'seller' => $this->seller,
                'applicant' => $this->applicant,
                'recipientEmail' => $notifiable->email,
            ]);
    }
}
