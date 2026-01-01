<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'service_id',
        'service_package_id',
        'conversation_id',
        'title',
        'description',
        'price',
        'platform_fee',
        'seller_amount',
        'delivery_days',
        'revisions_allowed',
        'revisions_used',
        'status',
        'requirements_data',
        'delivery_notes',
        'completion_notes',
        'due_at',
        'started_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'auto_complete_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'requirements_data' => 'array',
        'due_at' => 'datetime',
        'started_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_complete_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'SVC-' . strtoupper(Str::random(8));
            }
        });
    }

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

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ServiceDelivery::class);
    }

    public function latestDelivery(): HasOne
    {
        return $this->hasOne(ServiceDelivery::class)->latestOfMany();
    }

    public function escrowTransaction(): MorphOne
    {
        return $this->morphOne(EscrowTransaction::class, 'escrowable');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'disputable_id')
            ->where('disputable_type', self::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_payment', 'pending_requirements', 'ordered']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled', 'disputed']);
    }

    // Status checks
    public function canStart(): bool
    {
        return $this->status === 'ordered';
    }

    public function canDeliver(): bool
    {
        return in_array($this->status, ['in_progress', 'revision_requested']);
    }

    public function canRequestRevision(): bool
    {
        if ($this->status !== 'delivered') {
            return false;
        }
        if ($this->revisions_allowed > 0 && $this->revisions_used >= $this->revisions_allowed) {
            return false;
        }
        return true;
    }

    public function canApprove(): bool
    {
        return $this->status === 'delivered';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending_payment', 'pending_requirements', 'ordered']);
    }

    public function canDispute(): bool
    {
        return in_array($this->status, ['in_progress', 'delivered', 'revision_requested']);
    }

    public function isOverdue(): bool
    {
        return $this->due_at && $this->due_at->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return format_price($this->price);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public const STATUSES = [
        'pending_payment' => 'Pending Payment',
        'pending_requirements' => 'Pending Requirements',
        'ordered' => 'Ordered',
        'in_progress' => 'In Progress',
        'delivered' => 'Delivered',
        'revision_requested' => 'Revision Requested',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'disputed' => 'Disputed',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
