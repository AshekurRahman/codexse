<?php

namespace App\Services;

use App\Models\ProcessedWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebhookProtectionService
{
    /**
     * Get maximum age of webhook in seconds.
     */
    protected function getMaxWebhookAge(): int
    {
        return (int) config('security.webhooks.replay_ttl', 300);
    }

    /**
     * Get cache TTL for nonce storage in seconds.
     */
    protected function getNonceCacheTtl(): int
    {
        // Cache for 1 hour or webhook cleanup days * 24 hours, whichever is shorter
        $cleanupDays = config('security.webhooks.cleanup_days', 7);
        return min(3600, $cleanupDays * 86400);
    }

    /**
     * Validate webhook timestamp to prevent replay of old webhooks.
     *
     * @param int|string|null $timestamp Unix timestamp or ISO 8601 string
     * @param int $maxAge Maximum age in seconds
     * @return bool True if timestamp is valid
     */
    public function validateTimestamp($timestamp, ?int $maxAge = null): bool
    {
        if ($timestamp === null) {
            return true; // Allow webhooks without timestamp (provider handles security)
        }

        $maxAge = $maxAge ?? $this->getMaxWebhookAge();

        // Convert ISO 8601 to timestamp if needed
        if (!is_numeric($timestamp)) {
            $parsed = strtotime($timestamp);
            if ($parsed === false) {
                Log::warning('Webhook invalid timestamp format', ['timestamp' => $timestamp]);
                return false;
            }
            $timestamp = $parsed;
        }

        $age = abs(time() - (int)$timestamp);

        if ($age > $maxAge) {
            Log::warning('Webhook timestamp expired', [
                'timestamp' => $timestamp,
                'age_seconds' => $age,
                'max_age' => $maxAge,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Validate nonce to prevent duplicate processing.
     * Uses cache for quick lookups and database for persistence.
     *
     * @param string $nonce The unique nonce/event ID
     * @param string $provider The webhook provider
     * @return bool True if nonce is valid (not previously used)
     */
    public function validateNonce(string $nonce, string $provider = 'generic'): bool
    {
        $cacheKey = "webhook_nonce:{$provider}:{$nonce}";

        // Check cache first for fast rejection
        if (Cache::has($cacheKey)) {
            Log::warning('Webhook nonce replay detected (cache)', [
                'provider' => $provider,
                'nonce' => $nonce,
            ]);
            return false;
        }

        // Check database
        if (ProcessedWebhook::isProcessed($provider, $nonce)) {
            // Also cache it to speed up future checks
            Cache::put($cacheKey, true, $this->getNonceCacheTtl());

            Log::warning('Webhook nonce replay detected (database)', [
                'provider' => $provider,
                'nonce' => $nonce,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Register a nonce as used.
     *
     * @param string $nonce The unique nonce/event ID
     * @param string $provider The webhook provider
     * @param string|null $eventType Type of webhook event
     * @param \DateTimeInterface|null $eventTimestamp Original event timestamp
     * @param array $metadata Additional metadata
     * @param string|null $sourceIp Source IP address
     * @return ProcessedWebhook
     */
    public function registerNonce(
        string $nonce,
        string $provider = 'generic',
        ?string $eventType = null,
        ?\DateTimeInterface $eventTimestamp = null,
        array $metadata = [],
        ?string $sourceIp = null
    ): ProcessedWebhook {
        $cacheKey = "webhook_nonce:{$provider}:{$nonce}";

        // Add to cache
        Cache::put($cacheKey, true, $this->getNonceCacheTtl());

        // Add to database
        return ProcessedWebhook::markProcessed(
            $provider,
            $nonce,
            $eventType,
            $eventTimestamp,
            $metadata,
            $sourceIp
        );
    }

    /**
     * Attempt to acquire an exclusive lock on a webhook for processing.
     * This is atomic and prevents race conditions.
     *
     * @param string $eventId The unique event ID
     * @param string $provider The webhook provider
     * @param string|null $eventType Type of webhook event
     * @param \DateTimeInterface|null $eventTimestamp Original event timestamp
     * @param array $metadata Additional metadata
     * @param string|null $sourceIp Source IP address
     * @return ProcessedWebhook|null Returns the record if lock acquired, null if already processed
     */
    public function acquireLock(
        string $eventId,
        string $provider,
        ?string $eventType = null,
        ?\DateTimeInterface $eventTimestamp = null,
        array $metadata = [],
        ?string $sourceIp = null
    ): ?ProcessedWebhook {
        $cacheKey = "webhook_nonce:{$provider}:{$eventId}";

        // Quick check cache first
        if (Cache::has($cacheKey)) {
            return null;
        }

        // Try to acquire database lock
        if (!ProcessedWebhook::acquireProcessingLock(
            $provider,
            $eventId,
            $eventType,
            $eventTimestamp,
            $metadata,
            $sourceIp
        )) {
            return null;
        }

        // Add to cache
        Cache::put($cacheKey, true, $this->getNonceCacheTtl());

        // Return the created record
        $key = ProcessedWebhook::generateIdempotencyKey($provider, $eventId);
        return ProcessedWebhook::where('idempotency_key', $key)->first();
    }

    /**
     * Validate a Stripe webhook event.
     * Extracts event ID and created timestamp from Stripe event.
     *
     * @param object $event Stripe Event object
     * @param Request $request The HTTP request
     * @return array{valid: bool, webhook: ?ProcessedWebhook, error: ?string}
     */
    public function validateStripeEvent(object $event, Request $request): array
    {
        $eventId = $event->id;
        $eventTimestamp = isset($event->created) ? \Carbon\Carbon::createFromTimestamp($event->created) : null;
        $eventType = $event->type ?? 'unknown';

        // Validate timestamp (Stripe events older than 5 minutes)
        if ($eventTimestamp && !$this->validateTimestamp($event->created)) {
            return [
                'valid' => false,
                'webhook' => null,
                'error' => 'Stripe webhook expired',
            ];
        }

        // Try to acquire lock
        $webhook = $this->acquireLock(
            $eventId,
            'stripe',
            $eventType,
            $eventTimestamp,
            [
                'livemode' => $event->livemode ?? false,
                'api_version' => $event->api_version ?? null,
            ],
            $request->ip()
        );

        if (!$webhook) {
            return [
                'valid' => false,
                'webhook' => null,
                'error' => 'Duplicate Stripe webhook',
            ];
        }

        return [
            'valid' => true,
            'webhook' => $webhook,
            'error' => null,
        ];
    }

    /**
     * Validate a generic webhook with custom headers.
     *
     * @param Request $request The HTTP request
     * @param string $provider Provider name
     * @param string|null $timestampHeader Header containing timestamp
     * @param string|null $nonceHeader Header containing nonce
     * @return array{valid: bool, webhook: ?ProcessedWebhook, error: ?string}
     */
    public function validateGenericWebhook(
        Request $request,
        string $provider,
        ?string $timestampHeader = 'X-Webhook-Timestamp',
        ?string $nonceHeader = 'X-Webhook-Nonce'
    ): array {
        $timestamp = $timestampHeader ? $request->header($timestampHeader) : null;
        $nonce = $nonceHeader ? $request->header($nonceHeader) : null;

        // If no nonce header, generate one from payload hash
        if (!$nonce) {
            $nonce = hash('sha256', $request->getContent());
        }

        // Validate timestamp if present
        if ($timestamp && !$this->validateTimestamp($timestamp)) {
            return [
                'valid' => false,
                'webhook' => null,
                'error' => 'Webhook expired',
            ];
        }

        // Try to acquire lock
        $eventTimestamp = $timestamp ? \Carbon\Carbon::createFromTimestamp((int)$timestamp) : null;
        $webhook = $this->acquireLock(
            $nonce,
            $provider,
            null,
            $eventTimestamp,
            [],
            $request->ip()
        );

        if (!$webhook) {
            return [
                'valid' => false,
                'webhook' => null,
                'error' => 'Duplicate webhook',
            ];
        }

        return [
            'valid' => true,
            'webhook' => $webhook,
            'error' => null,
        ];
    }

    /**
     * Validate a Payoneer webhook.
     * Uses signature as nonce since Payoneer includes unique signatures.
     *
     * @param Request $request The HTTP request
     * @return array{valid: bool, webhook: ?ProcessedWebhook, error: ?string}
     */
    public function validatePayoneerWebhook(Request $request): array
    {
        // Payoneer uses X-Payoneer-Signature which is unique per request
        $signature = $request->header('X-Payoneer-Signature');

        if (!$signature) {
            // Generate nonce from payload hash if no signature
            $signature = hash('sha256', $request->getContent());
        }

        // Try to acquire lock
        $webhook = $this->acquireLock(
            $signature,
            'payoneer',
            null,
            null,
            [],
            $request->ip()
        );

        if (!$webhook) {
            return [
                'valid' => false,
                'webhook' => null,
                'error' => 'Duplicate Payoneer webhook',
            ];
        }

        return [
            'valid' => true,
            'webhook' => $webhook,
            'error' => null,
        ];
    }
}
