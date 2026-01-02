<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NewSaleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Collection $items,
        public float $totalEarnings
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Sale! ' . format_price($this->totalEarnings) . ' - ' . config('app.name'))
            ->view('emails.seller.new-sale', [
                'seller' => $notifiable,
                'order' => $this->order,
                'items' => $this->items,
                'totalEarnings' => $this->totalEarnings,
                'recipientEmail' => $notifiable->email,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $productName = $this->items->count() > 1
            ? $this->items->first()->product_name . ' +' . ($this->items->count() - 1) . ' more'
            : $this->items->first()->product_name;

        return [
            'type' => 'new_sale',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'product_name' => $productName,
            'total_earnings' => $this->totalEarnings,
            'items_count' => $this->items->count(),
            'message' => 'New sale: ' . format_price($this->totalEarnings),
            'url' => '/seller/orders',
        ];
    }
}
