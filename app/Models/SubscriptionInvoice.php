<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'stripe_invoice_id',
        'invoice_number',
        'amount',
        'tax',
        'total',
        'currency',
        'status',
        'paid_at',
        'due_date',
        'pdf_url',
        'line_items',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'line_items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'open']);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return format_price($this->amount);
    }

    public function getFormattedTotalAttribute(): string
    {
        return format_price($this->total);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'open' => 'Open',
            'paid' => 'Paid',
            'void' => 'Void',
            'uncollectible' => 'Uncollectible',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'success',
            'open' => 'warning',
            'draft' => 'info',
            'void', 'uncollectible' => 'danger',
            default => 'secondary',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
