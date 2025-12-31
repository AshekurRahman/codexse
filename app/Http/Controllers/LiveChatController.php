<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    /**
     * Start or resume a chat session.
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required_without:user_id|string|max:255',
            'email' => 'required_without:user_id|email|max:255',
            'department' => 'sometimes|in:general,sales,technical,billing',
            'subject' => 'sometimes|string|max:255',
        ]);

        $user = auth()->user();
        $sessionId = $request->session()->getId();

        // Check for existing active chat
        $chat = LiveChat::where(function ($q) use ($user, $sessionId) {
            if ($user) {
                $q->where('user_id', $user->id);
            } else {
                $q->where('session_id', $sessionId);
            }
        })
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        if (!$chat) {
            $chat = LiveChat::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'visitor_name' => $user ? $user->name : $request->name,
                'visitor_email' => $user ? $user->email : $request->email,
                'department' => $request->department ?? 'general',
                'subject' => $request->subject,
                'status' => 'waiting',
            ]);

            // Add welcome message
            $chat->messages()->create([
                'sender_type' => 'system',
                'message' => 'Welcome! A support agent will be with you shortly. Average wait time is under 2 minutes.',
            ]);
        }

        return response()->json([
            'chat' => $chat->load('messages.user'),
            'status' => 'success',
        ]);
    }

    /**
     * Send a message in the chat.
     */
    public function sendMessage(Request $request, LiveChat $chat): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Verify ownership
        if (!$this->canAccessChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($chat->isClosed()) {
            return response()->json(['error' => 'This chat has been closed'], 400);
        }

        $message = $chat->messages()->create([
            'user_id' => auth()->id(),
            'sender_type' => 'visitor',
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => $message->load('user'),
            'status' => 'success',
        ]);
    }

    /**
     * Get chat messages (for polling).
     */
    public function getMessages(Request $request, LiveChat $chat): JsonResponse
    {
        if (!$this->canAccessChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $chat->messages()
            ->when($lastMessageId, fn($q) => $q->where('id', '>', $lastMessageId))
            ->with('user')
            ->orderBy('id')
            ->get();

        // Mark agent messages as read
        $chat->markAsReadForVisitor();

        return response()->json([
            'messages' => $messages,
            'chat_status' => $chat->status,
            'agent' => $chat->agent ? [
                'name' => $chat->agent->name,
                'avatar' => $chat->agent->profile_photo_url ?? null,
            ] : null,
        ]);
    }

    /**
     * Get chat status.
     */
    public function getStatus(LiveChat $chat): JsonResponse
    {
        if (!$this->canAccessChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $chat->status,
            'agent' => $chat->agent ? [
                'name' => $chat->agent->name,
                'avatar' => $chat->agent->profile_photo_url ?? null,
            ] : null,
            'unread_count' => $chat->unreadMessagesForVisitor(),
        ]);
    }

    /**
     * End the chat.
     */
    public function endChat(Request $request, LiveChat $chat): JsonResponse
    {
        if (!$this->canAccessChat($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'feedback' => 'sometimes|string|max:1000',
        ]);

        $chat->update([
            'status' => 'closed',
            'ended_at' => now(),
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);

        $chat->messages()->create([
            'sender_type' => 'system',
            'message' => 'Chat ended by visitor.',
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Get existing active chat for the current user/session.
     */
    public function getActiveChat(): JsonResponse
    {
        $user = auth()->user();
        $sessionId = request()->session()->getId();

        $chat = LiveChat::where(function ($q) use ($user, $sessionId) {
            if ($user) {
                $q->where('user_id', $user->id);
            } else {
                $q->where('session_id', $sessionId);
            }
        })
            ->whereIn('status', ['waiting', 'active'])
            ->with(['messages.user', 'agent'])
            ->first();

        return response()->json([
            'chat' => $chat,
            'has_active_chat' => (bool) $chat,
        ]);
    }

    /**
     * Check if the current user can access the chat.
     */
    private function canAccessChat(LiveChat $chat): bool
    {
        $user = auth()->user();
        $sessionId = request()->session()->getId();

        if ($user && $chat->user_id === $user->id) {
            return true;
        }

        if (!$user && $chat->session_id === $sessionId) {
            return true;
        }

        return false;
    }

    /**
     * Get support availability status.
     */
    public function getAvailability(): JsonResponse
    {
        // In a real app, you'd check agent availability
        // For now, we'll return available during business hours
        $hour = now()->hour;
        $dayOfWeek = now()->dayOfWeek;

        $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;
        $isBusinessHours = $hour >= 9 && $hour < 18;

        return response()->json([
            'available' => true, // Always available for demo
            'message' => $isWeekday && $isBusinessHours
                ? 'Support agents are online'
                : 'Leave a message and we\'ll respond soon',
            'estimated_wait' => 'Under 2 minutes',
        ]);
    }
}
