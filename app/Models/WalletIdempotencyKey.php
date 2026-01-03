<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletIdempotencyKey extends Model
{
    public const OPERATION_PURCHASE = 'purchase';
    public const OPERATION_HOLD = 'hold';
    public const OPERATION_CAPTURE = 'capture';
    public const OPERATION_RELEASE = 'release';
    public const OPERATION_REFUND = 'refund';

    protected $fillable = [
        'key',
        'wallet_id',
        'operation',
        'transaction_id',
        'hold_id',
        'request_hash',
        'response',
        'expires_at',
    ];

    protected $casts = [
        'request_hash' => 'array',
        'response' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the wallet that owns the idempotency key.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the associated transaction.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'transaction_id');
    }

    /**
     * Get the associated hold.
     */
    public function hold(): BelongsTo
    {
        return $this->belongsTo(WalletHold::class, 'hold_id');
    }

    /**
     * Check if the idempotency key is still valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Check if the idempotency key has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope for valid (non-expired) keys.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope for expired keys.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for a specific operation type.
     */
    public function scopeForOperation($query, string $operation)
    {
        return $query->where('operation', $operation);
    }

    /**
     * Find a valid idempotency key by key string and wallet.
     */
    public static function findValid(string $key, int $walletId): ?self
    {
        return static::where('key', $key)
            ->where('wallet_id', $walletId)
            ->valid()
            ->first();
    }
}
