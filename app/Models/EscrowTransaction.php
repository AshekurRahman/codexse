<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'payer_id',
        'payee_id',
        'seller_id',
        'escrowable_type',
        'escrowable_id',
        'amount',
        'platform_fee',
        'seller_amount',
        'currency',
        'status',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'held_at',
        'released_at',
        'refunded_at',
        'auto_release_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'auto_release_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = 'ESC-' . strtoupper(Str::random(10));
            }
        });
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function escrowable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHeld($query)
    {
        return $query->where('status', 'held');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeReadyForAutoRelease($query)
    {
        return $query->where('status', 'held')
            ->whereNotNull('auto_release_at')
            ->where('auto_release_at', '<=', now());
    }

    // Status checks
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isHeld(): bool
    {
        return $this->status === 'held';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function canRelease(): bool
    {
        return $this->status === 'held';
    }

    public function canRefund(): bool
    {
        return in_array($this->status, ['pending', 'held']);
    }

    public function canDispute(): bool
    {
        return $this->status === 'held';
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return format_price($this->amount);
    }

    public function getFormattedFeeAttribute(): string
    {
        return format_price($this->platform_fee);
    }

    public function getFormattedSellerAmountAttribute(): string
    {
        return format_price($this->seller_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'held' => 'Held in Escrow',
        'released' => 'Released',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially Refunded',
        'disputed' => 'Disputed',
        'cancelled' => 'Cancelled',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
