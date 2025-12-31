<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceOrderDelivered extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Order Delivered - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your order has been delivered.')
            ->line('Order: ' . $this->order->title)
            ->line('Seller: ' . $this->order->seller->user->name)
            ->action('Review Delivery', url('/service-orders/' . $this->order->id))
            ->line('Please review the delivery and approve it if you are satisfied, or request a revision if needed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'service_order_delivered',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => $this->order->title,
            'message' => 'Order delivered: ' . $this->order->title,
            'url' => '/service-orders/' . $this->order->id,
        ];
    }
}
