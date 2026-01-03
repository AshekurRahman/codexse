<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletHold extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CAPTURED = 'captured';
    public const STATUS_RELEASED = 'released';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'idempotency_key',
        'amount',
        'balance_before',
        'status',
        'holdable_type',
        'holdable_id',
        'description',
        'metadata',
        'expires_at',
        'captured_at',
        'released_at',
        'captured_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'captured_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Get the wallet that owns the hold.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user that owns the hold.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the holdable model (Order, etc.).
     */
    public function holdable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the transaction created when funds were captured.
     */
    public function capturedTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'captured_transaction_id');
    }

    /**
     * Check if the hold is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the hold is captured.
     */
    public function isCaptured(): bool
    {
        return $this->status === self::STATUS_CAPTURED;
    }

    /**
     * Check if the hold is released.
     */
    public function isReleased(): bool
    {
        return $this->status === self::STATUS_RELEASED;
    }

    /**
     * Check if the hold is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the hold can be captured.
     */
    public function canCapture(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Check if the hold can be released.
     */
    public function canRelease(): bool
    {
        return $this->isPending();
    }

    /**
     * Scope for pending holds.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for expired holds that are still pending.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('expires_at', '<', now());
    }

    /**
     * Scope for holds belonging to a specific holdable.
     */
    public function scopeForHoldable($query, Model $holdable)
    {
        return $query->where('holdable_type', get_class($holdable))
            ->where('holdable_id', $holdable->id);
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get time remaining until expiration.
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if ($this->isExpired()) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }
}
