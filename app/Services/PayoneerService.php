<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayoneerService
{
    protected string $baseUrl;
    protected ?string $programId;
    protected ?string $apiUsername;
    protected ?string $apiPassword;
    protected bool $isLive;

    public function __construct()
    {
        // Database settings take priority, then config
        $configSandbox = config('services.payoneer.sandbox', true);
        $dbMode = Setting::get('payoneer_mode');

        $this->isLive = $dbMode !== null
            ? $dbMode === 'live'
            : !$configSandbox;

        $this->baseUrl = $this->isLive
            ? 'https://api.payoneer.com/v4'
            : 'https://api.sandbox.payoneer.com/v4';

        $this->programId = Setting::get('payoneer_program_id')
            ?: config('services.payoneer.program_id');
        $this->apiUsername = Setting::get('payoneer_api_username')
            ?: config('services.payoneer.username');
        $this->apiPassword = Setting::get('payoneer_api_password')
            ?: config('services.payoneer.password');
    }

    /**
     * Check if Payoneer is configured
     */
    public function isConfigured(): bool
    {
        return Setting::get('payoneer_enabled', false)
            && !empty($this->programId)
            && !empty($this->apiUsername)
            && !empty($this->apiPassword);
    }

    /**
     * Get the mode (sandbox or live)
     */
    public function getMode(): string
    {
        return $this->isLive ? 'live' : 'sandbox';
    }

    /**
     * Create a checkout session for Payoneer
     */
    public function createCheckoutSession(array $data): ?array
    {
        if (!$this->isConfigured()) {
            Log::error('Payoneer is not configured');
            return null;
        }

        try {
            $payload = [
                'transactionId' => $data['transaction_id'],
                'country' => $data['country'] ?? 'US',
                'payment' => [
                    'amount' => $data['amount'],
                    'currency' => strtoupper($data['currency'] ?? 'USD'),
                    'reference' => $data['reference'] ?? $data['transaction_id'],
                ],
                'customer' => [
                    'email' => $data['email'],
                    'name' => [
                        'firstName' => $data['first_name'] ?? '',
                        'lastName' => $data['last_name'] ?? '',
                    ],
                ],
                'callback' => [
                    'returnUrl' => $data['return_url'],
                    'cancelUrl' => $data['cancel_url'],
                    'notificationUrl' => $data['webhook_url'] ?? route('payoneer.webhook'),
                ],
                'style' => [
                    'language' => 'en',
                ],
            ];

            // Add line items if provided
            if (!empty($data['items'])) {
                $payload['products'] = array_map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'amount' => $item['price'],
                        'quantity' => $item['quantity'] ?? 1,
                    ];
                }, $data['items']);
            }

            $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                ->post("{$this->baseUrl}/programs/{$this->programId}/checkout", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Payoneer checkout creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Payoneer checkout exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get checkout status
     */
    public function getCheckoutStatus(string $transactionId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                ->get("{$this->baseUrl}/programs/{$this->programId}/checkout/{$transactionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Payoneer status check exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify webhook signature using HMAC-SHA256.
     *
     * Payoneer signs webhooks using HMAC-SHA256 with a shared secret.
     * The signature is sent in the X-Payoneer-Signature header.
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        $webhookSecret = Setting::get('payoneer_webhook_secret');

        // If no webhook secret is configured, reject all webhooks for security
        if (empty($webhookSecret)) {
            Log::warning('Payoneer webhook rejected: No webhook secret configured');
            return false;
        }

        // If no signature provided, reject the webhook
        if (empty($signature)) {
            Log::warning('Payoneer webhook rejected: No signature provided');
            return false;
        }

        // Calculate expected signature using HMAC-SHA256
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        // Use timing-safe comparison to prevent timing attacks
        $isValid = hash_equals($expectedSignature, $signature);

        if (!$isValid) {
            Log::warning('Payoneer webhook signature verification failed', [
                'provided_signature' => substr($signature, 0, 20) . '...',
            ]);
        }

        return $isValid;
    }

    /**
     * Process webhook event
     */
    public function processWebhook(array $data): bool
    {
        $transactionId = $data['transactionId'] ?? null;
        $status = $data['status'] ?? null;

        if (!$transactionId || !$status) {
            return false;
        }

        Log::info('Payoneer webhook received', [
            'transactionId' => $transactionId,
            'status' => $status,
        ]);

        return true;
    }

    /**
     * Get the redirect URL for the checkout
     */
    public function getCheckoutUrl(array $sessionData): ?string
    {
        return $sessionData['links']['redirect'] ?? $sessionData['redirectUrl'] ?? null;
    }
}
