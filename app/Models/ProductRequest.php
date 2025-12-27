<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ProductRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'product_title',
        'category_id',
        'description',
        'budget_min',
        'budget_max',
        'urgency',
        'features',
        'reference_urls',
        'attachments',
        'status',
        'admin_notes',
        'assigned_to',
        'fulfilled_by_product_id',
        'reviewed_at',
        'fulfilled_at',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'fulfilled_at' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWING = 'reviewing';
    const STATUS_APPROVED = 'approved';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CLOSED = 'closed';

    // Urgency constants
    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_HIGH = 'high';
    const URGENCY_URGENT = 'urgent';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REVIEWING => 'Reviewing',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_FULFILLED => 'Fulfilled',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function getUrgencies(): array
    {
        return [
            self::URGENCY_LOW => 'Low',
            self::URGENCY_NORMAL => 'Normal',
            self::URGENCY_HIGH => 'High',
            self::URGENCY_URGENT => 'Urgent',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function fulfilledByProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'fulfilled_by_product_id');
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeFulfilled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FULFILLED);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_REVIEWING,
            self::STATUS_APPROVED,
        ]);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_REVIEWING,
            self::STATUS_APPROVED,
        ]);
    }

    public function isFulfilled(): bool
    {
        return $this->status === self::STATUS_FULFILLED;
    }

    public function getBudgetRangeAttribute(): ?string
    {
        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min, 2) . ' - $' . number_format($this->budget_max, 2);
        } elseif ($this->budget_min) {
            return 'From $' . number_format($this->budget_min, 2);
        } elseif ($this->budget_max) {
            return 'Up to $' . number_format($this->budget_max, 2);
        }
        return null;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_REVIEWING => 'info',
            self::STATUS_APPROVED => 'primary',
            self::STATUS_FULFILLED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CLOSED => 'gray',
            default => 'gray',
        };
    }

    public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            self::URGENCY_LOW => 'gray',
            self::URGENCY_NORMAL => 'info',
            self::URGENCY_HIGH => 'warning',
            self::URGENCY_URGENT => 'danger',
            default => 'gray',
        };
    }

    public function markAsReviewing(): void
    {
        $this->update([
            'status' => self::STATUS_REVIEWING,
            'reviewed_at' => now(),
        ]);
    }

    public function markAsFulfilled(Product $product): void
    {
        $this->update([
            'status' => self::STATUS_FULFILLED,
            'fulfilled_by_product_id' => $product->id,
            'fulfilled_at' => now(),
        ]);
    }
}
