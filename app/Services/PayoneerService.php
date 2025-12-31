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
        $this->isLive = Setting::get('payoneer_mode', 'sandbox') === 'live';
        $this->baseUrl = $this->isLive
            ? 'https://api.payoneer.com/v4'
            : 'https://api.sandbox.payoneer.com/v4';

        $this->programId = Setting::get('payoneer_program_id');
        $this->apiUsername = Setting::get('payoneer_api_username');
        $this->apiPassword = Setting::get('payoneer_api_password');
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
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        // Implement webhook signature verification based on Payoneer's requirements
        // This is a placeholder - actual implementation depends on Payoneer's webhook security
        return true;
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
