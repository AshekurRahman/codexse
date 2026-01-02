<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_number',
        'order_id',
        'user_id',
        'processed_by',
        'amount',
        'type',
        'status',
        'reason',
        'admin_notes',
        'payment_method',
        'stripe_refund_id',
        'paypal_refund_id',
        'payoneer_refund_id',
        'processed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'rejected' => 'Rejected',
    ];

    public const TYPES = [
        'full' => 'Full Refund',
        'partial' => 'Partial Refund',
    ];

    public const REASONS = [
        'customer_request' => 'Customer Request',
        'defective_product' => 'Defective Product',
        'not_as_described' => 'Not As Described',
        'duplicate_order' => 'Duplicate Order',
        'never_received' => 'Never Received',
        'wrong_item' => 'Wrong Item',
        'other' => 'Other',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refund) {
            if (empty($refund->refund_number)) {
                $refund->refund_number = static::generateRefundNumber();
            }
        });
    }

    public static function generateRefundNumber(): string
    {
        do {
            $number = 'REF-' . strtoupper(substr(uniqid(), -8));
        } while (static::where('refund_number', $number)->exists());

        return $number;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canProcess(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'processing' => 'primary',
            'completed' => 'success',
            'failed' => 'danger',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
