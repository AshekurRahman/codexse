<?php

namespace App\Services;

use App\Models\Setting;
use Stripe\Stripe;

class StripeService
{
    protected ?string $secretKey = null;
    protected ?string $publicKey = null;
    protected ?string $webhookSecret = null;
    protected string $currency = 'usd';

    public function __construct()
    {
        $this->loadSettings();
        $this->initializeStripe();
    }

    protected function loadSettings(): void
    {
        // Try database settings first, fallback to config/env
        $this->secretKey = $this->getSetting('stripe_secret', config('stripe.secret'));
        $this->publicKey = $this->getSetting('stripe_key', config('stripe.key'));
        $this->webhookSecret = $this->getSetting('stripe_webhook_secret', config('stripe.webhook_secret'));
        $this->currency = $this->getSetting('stripe_currency', config('stripe.currency', 'usd'));
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

    protected function initializeStripe(): void
    {
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publicKey);
    }
}
