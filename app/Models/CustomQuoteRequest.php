<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomQuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'service_id',
        'conversation_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'deadline',
        'attachments',
        'status',
    ];

    protected $casts = [
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'service_id' => 'integer',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'deadline' => 'date',
        'attachments' => 'array',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function quote(): HasOne
    {
        return $this->hasOne(CustomQuote::class);
    }

    public function getBudgetRangeAttribute(): ?string
    {
        if ($this->budget_min && $this->budget_max) {
            return format_price($this->budget_min) . ' - ' . format_price($this->budget_max);
        } elseif ($this->budget_min) {
            return 'From ' . format_price($this->budget_min);
        } elseif ($this->budget_max) {
            return 'Up to ' . format_price($this->budget_max);
        }
        return null;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function hasQuote(): bool
    {
        return $this->status === 'quoted' && $this->quote()->exists();
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'quoted' => 'Quoted',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
