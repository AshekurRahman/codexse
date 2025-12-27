<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\ChatbotFaq;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected ?string $apiKey = null;
    protected string $model = 'gemini-2.0-flash';
    protected int $maxTokens = 1024;
    protected ?string $systemPrompt = null;
    protected int $rateLimitPerMinute = 10;
    protected int $rateLimitPerDay = 100;
    protected bool $enabled = false;
    protected string $mode = 'faq'; // 'faq' or 'ai'
    protected string $fallbackMessage = '';

    protected const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $this->apiKey = $this->getSetting('chatbot_api_key', config('services.gemini.api_key'));
        $this->model = $this->getSetting('chatbot_model', 'gemini-2.0-flash');
        $this->maxTokens = (int) $this->getSetting('chatbot_max_tokens', 1024);
        $this->systemPrompt = $this->getSetting('chatbot_system_prompt', $this->getDefaultSystemPrompt());
        $this->rateLimitPerMinute = (int) $this->getSetting('chatbot_rate_limit_per_minute', 10);
        $this->rateLimitPerDay = (int) $this->getSetting('chatbot_rate_limit_per_day', 100);
        $this->enabled = (bool) $this->getSetting('chatbot_enabled', false);
        $this->mode = $this->getSetting('chatbot_mode', 'faq');
        $this->fallbackMessage = $this->getSetting('chatbot_fallback_message',
            "I'm sorry, I don't have an answer for that question. Please try rephrasing or contact our support team for help."
        );
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
        // FAQ mode doesn't need API key
        if ($this->mode === 'faq') {
            return true;
        }
        return !empty($this->apiKey);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->isConfigured();
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function isFaqMode(): bool
    {
        return $this->mode === 'faq';
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

        // Route to appropriate handler based on mode
        if ($this->isFaqMode()) {
            return $this->handleFaqMessage($session, $userMessage);
        }

        return $this->handleAiMessage($session, $userMessage, $context);
    }

    /**
     * Handle FAQ-based response (free, no API required)
     */
    protected function handleFaqMessage(AiChatSession $session, string $userMessage): array
    {
        // Save user message
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // Find matching FAQ
        $faq = ChatbotFaq::findBestMatch($userMessage);

        if ($faq) {
            $responseContent = $faq->answer;
            $faq->recordHit(); // Track analytics
        } else {
            $responseContent = $this->fallbackMessage;
        }

        // Save assistant message
        $assistantMsg = AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $responseContent,
            'metadata' => [
                'mode' => 'faq',
                'matched_faq_id' => $faq?->id,
            ],
        ]);

        $session->incrementMessageCount(0);

        return [
            'success' => true,
            'message' => $responseContent,
            'message_id' => $assistantMsg->id,
        ];
    }

    /**
     * Handle AI-based response using Google Gemini
     */
    protected function handleAiMessage(AiChatSession $session, string $userMessage, array $context = []): array
    {
        // Build conversation history for Gemini format
        $contents = $this->buildGeminiContents($session, $userMessage);

        // Save user message
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        try {
            // Build Gemini API URL
            $apiUrl = self::GEMINI_API_URL . "/{$this->model}:generateContent?key={$this->apiKey}";

            // Call Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($apiUrl, [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $this->buildSystemPrompt($session, $context)]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $this->maxTokens,
                    'temperature' => 0.7,
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ],
            ]);

            if (!$response->successful()) {
                Log::channel('error')->error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to get response from AI. Please try again.',
                ];
            }

            $data = $response->json();

            // Extract response text from Gemini format
            $assistantContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Calculate tokens used (Gemini provides this in usageMetadata)
            $tokensUsed = ($data['usageMetadata']['promptTokenCount'] ?? 0) +
                          ($data['usageMetadata']['candidatesTokenCount'] ?? 0);

            // Save assistant message
            $assistantMsg = AiChatMessage::create([
                'ai_chat_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $assistantContent,
                'tokens_used' => $tokensUsed,
                'metadata' => [
                    'mode' => 'ai',
                    'provider' => 'gemini',
                    'model' => $this->model,
                    'usage' => $data['usageMetadata'] ?? null,
                ],
            ]);

            $session->incrementMessageCount($tokensUsed);

            return [
                'success' => true,
                'message' => $assistantContent,
                'message_id' => $assistantMsg->id,
            ];

        } catch (\Exception $e) {
            Log::channel('error')->error('Gemini chatbot exception', [
                'message' => $e->getMessage(),
                'session_id' => $session->id,
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred. Please try again.',
            ];
        }
    }

    /**
     * Build conversation contents in Gemini format
     */
    protected function buildGeminiContents(AiChatSession $session, string $currentMessage): array
    {
        $contents = [];

        // Get previous messages
        $messages = $session->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->take(20) // Limit context window
            ->get();

        foreach ($messages as $msg) {
            $contents[] = [
                'role' => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [
                    ['text' => $msg->content]
                ]
            ];
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $currentMessage]
            ]
        ];

        return $contents;
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

    /**
     * Get available Gemini models
     */
    public static function getAvailableModels(): array
    {
        return [
            'gemini-2.0-flash' => 'Gemini 2.0 Flash (Fast & Free)',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash (Fast)',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro (Advanced)',
        ];
    }
}
