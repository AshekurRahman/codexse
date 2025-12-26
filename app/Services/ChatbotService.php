<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected ?string $apiKey = null;
    protected string $model = 'claude-sonnet-4-20250514';
    protected int $maxTokens = 1024;
    protected ?string $systemPrompt = null;
    protected int $rateLimitPerMinute = 10;
    protected int $rateLimitPerDay = 100;
    protected bool $enabled = false;

    protected const API_URL = 'https://api.anthropic.com/v1/messages';
    protected const API_VERSION = '2023-06-01';

    public function __construct()
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $this->apiKey = $this->getSetting('chatbot_api_key', config('services.anthropic.api_key'));
        $this->model = $this->getSetting('chatbot_model', 'claude-sonnet-4-20250514');
        $this->maxTokens = (int) $this->getSetting('chatbot_max_tokens', 1024);
        $this->systemPrompt = $this->getSetting('chatbot_system_prompt', $this->getDefaultSystemPrompt());
        $this->rateLimitPerMinute = (int) $this->getSetting('chatbot_rate_limit_per_minute', 10);
        $this->rateLimitPerDay = (int) $this->getSetting('chatbot_rate_limit_per_day', 100);
        $this->enabled = (bool) $this->getSetting('chatbot_enabled', false);
    }

    protected function getSetting(string $key, $fallback = null): mixed
    {
        try {
            $value = Setting::get($key);
            return $value ?: $fallback;
        } catch (\Exception $e) {
            return $fallback;
        }
    }

    protected function getDefaultSystemPrompt(): string
    {
        return <<<PROMPT
You are a helpful customer support assistant for Codexse, a digital marketplace for software products.

Your responsibilities:
- Answer questions about products, purchases, and account issues
- Help with technical support for downloads and licenses
- Guide users through common processes
- Be friendly, professional, and concise
- If you don't know something, acknowledge it and suggest contacting human support via the support ticket system

Important context:
- Users can purchase digital products (themes, plugins, scripts)
- They receive license keys for their purchases
- They can download products from their dashboard
- Refund requests should be directed to the support ticket system
- For account issues, direct users to their profile settings

Keep responses helpful but concise. Use markdown formatting when helpful.
PROMPT;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->isConfigured();
    }

    public function checkRateLimit(string $identifier): array
    {
        $minuteKey = 'chatbot:' . $identifier . ':minute';
        $dayKey = 'chatbot:' . $identifier . ':day';

        // Check per-minute limit
        if (RateLimiter::tooManyAttempts($minuteKey, $this->rateLimitPerMinute)) {
            $seconds = RateLimiter::availableIn($minuteKey);
            return [
                'allowed' => false,
                'error' => "Rate limit exceeded. Please wait {$seconds} seconds.",
                'retry_after' => $seconds,
            ];
        }

        // Check daily limit
        if (RateLimiter::tooManyAttempts($dayKey, $this->rateLimitPerDay)) {
            return [
                'allowed' => false,
                'error' => 'Daily message limit reached. Please try again tomorrow.',
                'retry_after' => null,
            ];
        }

        return ['allowed' => true];
    }

    public function recordRateLimitHit(string $identifier): void
    {
        RateLimiter::hit('chatbot:' . $identifier . ':minute', 60);
        RateLimiter::hit('chatbot:' . $identifier . ':day', 86400);
    }

    public function sendMessage(AiChatSession $session, string $userMessage, array $context = []): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'Chatbot is currently unavailable.',
            ];
        }

        // Build conversation history
        $messages = $this->buildConversationHistory($session);
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // Save user message
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        try {
            // Call Claude API
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => self::API_VERSION,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post(self::API_URL, [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'system' => $this->buildSystemPrompt($session, $context),
                'messages' => $messages,
            ]);

            if (!$response->successful()) {
                Log::channel('error')->error('Chatbot API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to get response from AI. Please try again.',
                ];
            }

            $data = $response->json();
            $assistantContent = $data['content'][0]['text'] ?? '';
            $tokensUsed = ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0);

            // Save assistant message
            $assistantMsg = AiChatMessage::create([
                'ai_chat_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $assistantContent,
                'tokens_used' => $tokensUsed,
                'metadata' => [
                    'model' => $this->model,
                    'usage' => $data['usage'] ?? null,
                ],
            ]);

            $session->incrementMessageCount($tokensUsed);

            return [
                'success' => true,
                'message' => $assistantContent,
                'message_id' => $assistantMsg->id,
            ];

        } catch (\Exception $e) {
            Log::channel('error')->error('Chatbot exception', [
                'message' => $e->getMessage(),
                'session_id' => $session->id,
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred. Please try again.',
            ];
        }
    }

    protected function buildConversationHistory(AiChatSession $session): array
    {
        return $session->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->take(20) // Limit context window
            ->get()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();
    }

    protected function buildSystemPrompt(AiChatSession $session, array $context = []): string
    {
        $prompt = $this->systemPrompt ?: $this->getDefaultSystemPrompt();

        // Add user context if authenticated
        if ($session->user) {
            $prompt .= "\n\nCurrent user: {$session->user->name}";

            if ($session->user->isSeller()) {
                $prompt .= "\nThis user is also a seller on the platform.";
            }
        } else {
            $prompt .= "\n\nThis is a guest user (not logged in).";
        }

        // Add page context if provided
        if (!empty($context['current_page'])) {
            $prompt .= "\n\nThe user is currently viewing: {$context['current_page']}";
        }

        if (!empty($context['page_title'])) {
            $prompt .= "\nPage title: {$context['page_title']}";
        }

        return $prompt;
    }

    public function getWelcomeMessage(): string
    {
        return $this->getSetting('chatbot_welcome_message',
            "Hello! I'm here to help with any questions about our products and services. How can I assist you today?"
        );
    }

    public function getOfflineMessage(): string
    {
        return $this->getSetting('chatbot_offline_message',
            'Our AI assistant is currently unavailable. Please submit a support ticket for assistance.'
        );
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getRateLimits(): array
    {
        return [
            'per_minute' => $this->rateLimitPerMinute,
            'per_day' => $this->rateLimitPerDay,
        ];
    }
}
