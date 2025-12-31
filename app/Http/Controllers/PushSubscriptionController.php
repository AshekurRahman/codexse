<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = $request->user();

        // Create or update subscription
        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $request->input('endpoint'),
            ],
            [
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                'user_agent' => $request->userAgent(),
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        // Ensure notification preferences exist
        NotificationPreference::getOrCreate($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Push subscription registered successfully.',
            'subscription_id' => $subscription->id,
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = $request->user();

        $deleted = PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $request->input('endpoint'))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted ? 'Subscription removed.' : 'No subscription found.',
        ]);
    }

    public function getVapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.webpush.public_key'),
        ]);
    }

    public function getPreferences(Request $request): JsonResponse
    {
        $preferences = NotificationPreference::getOrCreate($request->user()->id);

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'push_enabled' => 'sometimes|boolean',
            'notify_orders' => 'sometimes|boolean',
            'notify_messages' => 'sometimes|boolean',
            'notify_sales' => 'sometimes|boolean',
            'notify_reviews' => 'sometimes|boolean',
            'notify_promotions' => 'sometimes|boolean',
        ]);

        $preferences = NotificationPreference::getOrCreate($request->user()->id);
        $preferences->update($request->only([
            'push_enabled',
            'notify_orders',
            'notify_messages',
            'notify_sales',
            'notify_reviews',
            'notify_promotions',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully.',
            'preferences' => $preferences->fresh(),
        ]);
    }

    public function getSubscriptions(Request $request): JsonResponse
    {
        $subscriptions = PushSubscription::forUser($request->user()->id)
            ->active()
            ->get(['id', 'user_agent', 'last_used_at', 'created_at']);

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function removeSubscription(Request $request, PushSubscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription removed.',
        ]);
    }
}
