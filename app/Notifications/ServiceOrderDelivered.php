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
            ->view('emails.service.order-delivered', [
                'order' => $this->order,
                'delivery' => $this->order->latestDelivery ?? null,
                'recipientEmail' => $notifiable->email,
            ]);
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
