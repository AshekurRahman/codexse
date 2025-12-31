<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            return (new MailMessage)
                ->subject('New Service Order Received - ' . $this->order->order_number)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('You have received a new order for your service.')
                ->line('Order: ' . $this->order->title)
                ->line('Amount: $' . number_format($this->order->seller_amount, 2))
                ->line('Buyer: ' . $this->order->buyer->name)
                ->action('View Order', url('/seller/service-orders/' . $this->order->id))
                ->line('Please review the order requirements and start working on it.');
        }

        return (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order has been placed successfully.')
            ->line('Order: ' . $this->order->title)
            ->line('Amount: $' . number_format($this->order->price, 2))
            ->line('Seller: ' . $this->order->seller->user->name)
            ->action('View Order', url('/service-orders/' . $this->order->id))
            ->line('The seller will start working on your order soon.');
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
