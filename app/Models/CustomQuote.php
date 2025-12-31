<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_quote_request_id',
        'price',
        'delivery_days',
        'revisions',
        'description',
        'deliverables',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deliverables' => 'array',
        'expires_at' => 'datetime',
    ];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(CustomQuoteRequest::class, 'custom_quote_request_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function canAccept(): bool
    {
        return $this->isPending();
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getDeliveryTextAttribute(): string
    {
        if ($this->delivery_days == 1) {
            return '1 day delivery';
        }
        return $this->delivery_days . ' days delivery';
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
