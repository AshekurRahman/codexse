<?php

namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
        $this->webPush->setReuseVAPIDHeaders(true);
    }

    public function sendToUser(User $user, string $type, array $payload): int
    {
        // Check user preferences
        $preferences = NotificationPreference::getOrCreate($user->id);

        if (!$preferences->shouldNotify($type)) {
            return 0;
        }

        // Get active subscriptions
        $subscriptions = PushSubscription::forUser($user->id)->active()->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $sentCount = 0;

        foreach ($subscriptions as $subscription) {
            $sent = $this->sendNotification($subscription, $payload);
            if ($sent) {
                $sentCount++;
                $subscription->markAsUsed();
            }
        }

        return $sentCount;
    }

    public function sendToUsers(array $userIds, string $type, array $payload): int
    {
        $totalSent = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $totalSent += $this->sendToUser($user, $type, $payload);
            }
        }

        return $totalSent;
    }

    protected function sendNotification(PushSubscription $pushSubscription, array $payload): bool
    {
        try {
            $subscription = Subscription::create([
                'endpoint' => $pushSubscription->endpoint,
                'keys' => [
                    'p256dh' => $pushSubscription->p256dh_key,
                    'auth' => $pushSubscription->auth_token,
                ],
                'contentEncoding' => $pushSubscription->content_encoding,
            ]);

            $report = $this->webPush->sendOneNotification(
                $subscription,
                json_encode($payload)
            );

            if ($report->isSuccess()) {
                return true;
            }

            // Handle expired/invalid subscriptions
            if ($report->isSubscriptionExpired()) {
                $pushSubscription->deactivate();
                Log::info('Push subscription expired and deactivated', [
                    'subscription_id' => $pushSubscription->id,
                    'user_id' => $pushSubscription->user_id,
                ]);
            } else {
                Log::warning('Push notification failed', [
                    'subscription_id' => $pushSubscription->id,
                    'reason' => $report->getReason(),
                ]);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Push notification error', [
                'subscription_id' => $pushSubscription->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // Convenience methods for different notification types
    public function notifyNewOrder(User $user, array $orderData): int
    {
        return $this->sendToUser($user, 'order', [
            'title' => 'New Order Received!',
            'body' => "Order #{$orderData['order_id']} - {$orderData['product_name']}",
            'icon' => '/images/icons/order-icon.png',
            'badge' => '/images/icons/badge.png',
            'tag' => 'order-' . $orderData['order_id'],
            'data' => [
                'type' => 'order',
                'url' => $orderData['url'] ?? '/dashboard',
                'order_id' => $orderData['order_id'],
            ],
        ]);
    }

    public function notifyOrderUpdate(User $user, array $orderData): int
    {
        return $this->sendToUser($user, 'order', [
            'title' => 'Order Update',
            'body' => "Order #{$orderData['order_id']}: {$orderData['status']}",
            'icon' => '/images/icons/order-icon.png',
            'badge' => '/images/icons/badge.png',
            'tag' => 'order-' . $orderData['order_id'],
            'data' => [
                'type' => 'order',
                'url' => $orderData['url'] ?? '/purchases',
                'order_id' => $orderData['order_id'],
            ],
        ]);
    }

    public function notifyNewMessage(User $user, array $messageData): int
    {
        return $this->sendToUser($user, 'message', [
            'title' => "Message from {$messageData['sender_name']}",
            'body' => $this->truncateMessage($messageData['content'], 100),
            'icon' => $messageData['sender_avatar'] ?? '/images/icons/message-icon.png',
            'badge' => '/images/icons/badge.png',
            'tag' => 'message-' . $messageData['conversation_id'],
            'data' => [
                'type' => 'message',
                'url' => $messageData['url'] ?? '/conversations',
                'conversation_id' => $messageData['conversation_id'],
            ],
        ]);
    }

    public function notifyNewSale(User $seller, array $saleData): int
    {
        return $this->sendToUser($seller, 'sale', [
            'title' => 'You made a sale!',
            'body' => "{$saleData['product_name']} - {$saleData['amount']}",
            'icon' => '/images/icons/sale-icon.png',
            'badge' => '/images/icons/badge.png',
            'tag' => 'sale-' . $saleData['order_id'],
            'data' => [
                'type' => 'sale',
                'url' => $saleData['url'] ?? '/seller/orders',
                'order_id' => $saleData['order_id'],
            ],
        ]);
    }

    public function notifyNewReview(User $seller, array $reviewData): int
    {
        return $this->sendToUser($seller, 'review', [
            'title' => 'New Review',
            'body' => "{$reviewData['rating']} stars on {$reviewData['product_name']}",
            'icon' => '/images/icons/review-icon.png',
            'badge' => '/images/icons/badge.png',
            'tag' => 'review-' . $reviewData['review_id'],
            'data' => [
                'type' => 'review',
                'url' => $reviewData['url'] ?? '/seller/reviews',
                'review_id' => $reviewData['review_id'],
            ],
        ]);
    }

    protected function truncateMessage(string $message, int $length): string
    {
        $message = strip_tags($message);
        if (strlen($message) <= $length) {
            return $message;
        }
        return substr($message, 0, $length) . '...';
    }
}
