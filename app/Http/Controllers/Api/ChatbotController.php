<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatSession;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {}

    /**
     * Get or create a chat session
     */
    public function session(Request $request): JsonResponse
    {
        if (!$this->chatbotService->isEnabled()) {
            return response()->json([
                'enabled' => false,
                'message' => $this->chatbotService->getOfflineMessage(),
            ]);
        }

        $sessionId = $request->cookie('ai_chat_session') ?? (string) Str::uuid();
        $userId = auth()->id();

        // Find existing active session or create new one
        $session = AiChatSession::where('status', 'active')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            }, function ($query) use ($sessionId) {
                return $query->where('session_id', $sessionId)->whereNull('user_id');
            })
            ->first();

        if (!$session) {
            $session = AiChatSession::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'enabled' => true,
            'session_id' => $session->session_id,
            'welcome_message' => $this->chatbotService->getWelcomeMessage(),
            'messages' => $session->messages()
                ->whereIn('role', ['user', 'assistant'])
                ->orderBy('created_at')
                ->take(50)
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'role' => $m->role,
                    'content' => $m->content,
                    'created_at' => $m->created_at->toIso8601String(),
                ]),
        ])->cookie('ai_chat_session', $session->session_id, 60 * 24 * 30); // 30 days
    }

    /**
     * Send a message to the chatbot
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'required|uuid',
            'context' => 'nullable|array',
        ]);

        if (!$this->chatbotService->isEnabled()) {
            return response()->json([
                'success' => false,
                'error' => $this->chatbotService->getOfflineMessage(),
            ], 503);
        }

        // Find session
        $session = AiChatSession::where('session_id', $request->session_id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or expired. Please refresh the page.',
            ], 404);
        }

        // Check authorization - user can only access their own session
        $userId = auth()->id();
        if ($session->user_id && $session->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        // If user is now logged in but session was guest, link it
        if ($userId && !$session->user_id) {
            $session->update(['user_id' => $userId]);
        }

        // Rate limiting
        $identifier = $userId ?? $request->ip();
        $rateCheck = $this->chatbotService->checkRateLimit($identifier);

        if (!$rateCheck['allowed']) {
            return response()->json([
                'success' => false,
                'error' => $rateCheck['error'],
                'retry_after' => $rateCheck['retry_after'] ?? null,
            ], 429);
        }

        // Record rate limit hit before making API call
        $this->chatbotService->recordRateLimitHit($identifier);

        // Send message
        $result = $this->chatbotService->sendMessage(
            $session,
            $request->message,
            $request->context ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Close a chat session
     */
    public function close(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|uuid',
        ]);

        $session = AiChatSession::where('session_id', $request->session_id)->first();

        if ($session) {
            // Authorization check
            $userId = auth()->id();
            if ($session->user_id && $session->user_id !== $userId) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $session->close();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Start a new chat session (close old one and create new)
     */
    public function newSession(Request $request): JsonResponse
    {
        if (!$this->chatbotService->isEnabled()) {
            return response()->json([
                'enabled' => false,
                'message' => $this->chatbotService->getOfflineMessage(),
            ]);
        }

        $sessionId = $request->cookie('ai_chat_session');
        $userId = auth()->id();

        // Close existing session if any
        if ($sessionId) {
            AiChatSession::where('session_id', $sessionId)
                ->where('status', 'active')
                ->update(['status' => 'closed']);
        }

        // Create new session
        $newSessionId = (string) Str::uuid();
        $session = AiChatSession::create([
            'session_id' => $newSessionId,
            'user_id' => $userId,
        ]);

        return response()->json([
            'enabled' => true,
            'session_id' => $session->session_id,
            'welcome_message' => $this->chatbotService->getWelcomeMessage(),
            'messages' => [],
        ])->cookie('ai_chat_session', $session->session_id, 60 * 24 * 30);
    }
}
