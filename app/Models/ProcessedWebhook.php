<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProcessedWebhook extends Model
{
    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'idempotency_key',
        'event_timestamp',
        'status',
        'metadata',
        'source_ip',
    ];

    protected function casts(): array
    {
        return [
            'event_timestamp' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Generate idempotency key from provider and event ID.
     */
    public static function generateIdempotencyKey(string $provider, string $eventId): string
    {
        return hash('sha256', "{$provider}:{$eventId}");
    }

    /**
     * Check if a webhook has already been processed.
     */
    public static function isProcessed(string $provider, string $eventId): bool
    {
        $key = self::generateIdempotencyKey($provider, $eventId);

        return self::where('idempotency_key', $key)->exists();
    }

    /**
     * Mark a webhook as processed.
     */
    public static function markProcessed(
        string $provider,
        string $eventId,
        ?string $eventType = null,
        ?\DateTimeInterface $eventTimestamp = null,
        array $metadata = [],
        ?string $sourceIp = null
    ): self {
        $key = self::generateIdempotencyKey($provider, $eventId);

        return self::create([
            'provider' => $provider,
            'event_id' => $eventId,
            'event_type' => $eventType,
            'idempotency_key' => $key,
            'event_timestamp' => $eventTimestamp,
            'status' => 'processed',
            'metadata' => $metadata,
            'source_ip' => $sourceIp,
        ]);
    }

    /**
     * Attempt to acquire lock on webhook for processing.
     * Returns true if lock acquired, false if already processed.
     */
    public static function acquireProcessingLock(
        string $provider,
        string $eventId,
        ?string $eventType = null,
        ?\DateTimeInterface $eventTimestamp = null,
        array $metadata = [],
        ?string $sourceIp = null
    ): bool {
        $key = self::generateIdempotencyKey($provider, $eventId);

        try {
            self::create([
                'provider' => $provider,
                'event_id' => $eventId,
                'event_type' => $eventType,
                'idempotency_key' => $key,
                'event_timestamp' => $eventTimestamp,
                'status' => 'processing',
                'metadata' => $metadata,
                'source_ip' => $sourceIp,
            ]);

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            // Unique constraint violation - webhook already being processed
            if ($e->getCode() === '23000') {
                Log::warning('Webhook replay attempt detected', [
                    'provider' => $provider,
                    'event_id' => $eventId,
                    'source_ip' => $sourceIp,
                ]);
                return false;
            }
            throw $e;
        }
    }

    /**
     * Mark webhook as successfully processed.
     */
    public function markCompleted(): void
    {
        $this->update(['status' => 'processed']);
    }

    /**
     * Mark webhook as failed.
     */
    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Cleanup old processed webhooks.
     */
    public static function cleanup(int $daysToKeep = 30): int
    {
        return self::where('created_at', '<', now()->subDays($daysToKeep))->delete();
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
