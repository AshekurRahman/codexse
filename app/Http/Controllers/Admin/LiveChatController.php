<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveChatController extends Controller
{
    /**
     * Display the live chat dashboard for agents.
     */
    public function index(): View
    {
        $waitingChats = LiveChat::where('status', 'waiting')
            ->with(['user', 'latestMessage'])
            ->latest()
            ->get();

        $activeChats = LiveChat::where('status', 'active')
            ->where('agent_id', auth()->id())
            ->with(['user', 'latestMessage'])
            ->latest()
            ->get();

        return view('admin.live-chat.index', compact('waitingChats', 'activeChats'));
    }

    /**
     * Show a specific chat.
     */
    public function show(LiveChat $chat): View
    {
        $chat->load(['messages.user', 'user', 'agent']);

        // Mark messages as read
        $chat->markAsReadForAgent();

        return view('admin.live-chat.show', compact('chat'));
    }

    /**
     * Accept/claim a waiting chat.
     */
    public function accept(LiveChat $chat): JsonResponse
    {
        if (!$chat->isWaiting()) {
            return response()->json(['error' => 'Chat is no longer available'], 400);
        }

        $chat->assignAgent(auth()->user());

        return response()->json([
            'status' => 'success',
            'chat' => $chat->load('messages'),
        ]);
    }

    /**
     * Send a message as agent.
     */
    public function sendMessage(Request $request, LiveChat $chat): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        if ($chat->isClosed()) {
            return response()->json(['error' => 'This chat has been closed'], 400);
        }

        // Auto-assign if not assigned
        if (!$chat->agent_id) {
            $chat->assignAgent(auth()->user());
        }

        $message = $chat->messages()->create([
            'user_id' => auth()->id(),
            'sender_type' => 'agent',
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
        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $chat->messages()
            ->when($lastMessageId, fn($q) => $q->where('id', '>', $lastMessageId))
            ->with('user')
            ->orderBy('id')
            ->get();

        // Mark visitor messages as read
        $chat->markAsReadForAgent();

        return response()->json([
            'messages' => $messages,
            'chat_status' => $chat->status,
        ]);
    }

    /**
     * Close the chat.
     */
    public function close(LiveChat $chat): JsonResponse
    {
        $chat->close();

        return response()->json(['status' => 'success']);
    }

    /**
     * Transfer chat to another department.
     */
    public function transfer(Request $request, LiveChat $chat): JsonResponse
    {
        $request->validate([
            'department' => 'required|in:general,sales,technical,billing',
        ]);

        $oldDepartment = LiveChat::DEPARTMENTS[$chat->department];
        $newDepartment = LiveChat::DEPARTMENTS[$request->department];

        $chat->update([
            'department' => $request->department,
            'agent_id' => null,
            'status' => 'waiting',
        ]);

        $chat->messages()->create([
            'sender_type' => 'system',
            'message' => "Chat transferred from {$oldDepartment} to {$newDepartment}.",
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Get waiting chat count for notifications.
     */
    public function getWaitingCount(): JsonResponse
    {
        $count = LiveChat::where('status', 'waiting')->count();
        $myActive = LiveChat::where('status', 'active')
            ->where('agent_id', auth()->id())
            ->count();

        return response()->json([
            'waiting' => $count,
            'my_active' => $myActive,
        ]);
    }

    /**
     * Get quick responses/canned messages.
     */
    public function getQuickResponses(): JsonResponse
    {
        $responses = [
            ['id' => 1, 'title' => 'Greeting', 'message' => 'Hello! How can I help you today?'],
            ['id' => 2, 'title' => 'Thank You', 'message' => 'Thank you for contacting us! Is there anything else I can help you with?'],
            ['id' => 3, 'title' => 'Processing Time', 'message' => 'Orders typically process within 24 hours. You\'ll receive an email confirmation once your order is ready.'],
            ['id' => 4, 'title' => 'Refund Policy', 'message' => 'Our refund policy allows for refunds within 30 days of purchase. Would you like me to help you with a refund request?'],
            ['id' => 5, 'title' => 'Technical Support', 'message' => 'I\'d be happy to help with your technical issue. Could you please provide more details about the problem you\'re experiencing?'],
            ['id' => 6, 'title' => 'Closing', 'message' => 'Thank you for chatting with us today! If you have any more questions, feel free to reach out. Have a great day!'],
        ];

        return response()->json($responses);
    }
}
