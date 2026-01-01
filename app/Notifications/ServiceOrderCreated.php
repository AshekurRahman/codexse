<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class ServiceOrderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ServiceOrder $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isSeller = $notifiable->id === $this->order->seller->user_id;

        if ($isSeller) {
            // Use purpose-based template for seller
            return (new MailMessage)
                ->subject('New Service Order Received - ' . $this->order->order_number)
                ->view('emails.service.order-received-seller', [
                    'order' => $this->order,
                    'recipientEmail' => $notifiable->email,
                ]);
        }

        // Use purpose-based template for buyer
        return (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->view('emails.service.order-confirmation-buyer', [
                'order' => $this->order,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $isSeller = $notifiable->id === $this->order->seller->user_id;

        return [
            'type' => 'service_order_created',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => $this->order->title,
            'amount' => $isSeller ? $this->order->seller_amount : $this->order->price,
            'message' => $isSeller
                ? 'New order received: ' . $this->order->title
                : 'Order placed: ' . $this->order->title,
            'url' => $isSeller
                ? '/seller/service-orders/' . $this->order->id
                : '/service-orders/' . $this->order->id,
        ];
    }
}
