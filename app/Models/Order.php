<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'email',
        'subtotal',
        'discount',
        'tax_rate',
        'tax_amount',
        'total',
        'currency',
        'status',
        'payment_method',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'payoneer_transaction_id',
        'paypal_order_id',
        'billing_address',
        'billing_state',
        'coupon_code',
        'notes',
        'paid_at',
        'ip_address',
        'fraud_score',
        'user_agent',
        'wallet_hold_id',
        'wallet_transaction_id',
        'wallet_amount',
        'secondary_payment_method',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'wallet_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'fraud_score' => 'decimal:2',
            // Note: Payment session/order IDs are NOT encrypted because they need to be searchable
            // These are just reference IDs, not sensitive payment details
            // Encrypt only truly sensitive data
            'billing_address' => 'encrypted:array',
            'ip_address' => 'encrypted',
        ];
    }

    /**
     * Get formatted tax amount.
     */
    public function getFormattedTaxAmountAttribute(): string
    {
        return format_price($this->tax_amount ?? 0);
    }

    /**
     * Get formatted tax rate.
     */
    public function getFormattedTaxRateAttribute(): ?string
    {
        if ($this->tax_rate === null || $this->tax_rate == 0) {
            return null;
        }

        return number_format($this->tax_rate, 2) . '%';
    }

    /**
     * Get billing state name.
     */
    public function getBillingStateNameAttribute(): ?string
    {
        if (empty($this->billing_state)) {
            return null;
        }

        return config("tax.states.{$this->billing_state}");
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'CDX-' . strtoupper(uniqid());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the wallet hold associated with this order.
     */
    public function walletHold(): BelongsTo
    {
        return $this->belongsTo(WalletHold::class, 'wallet_hold_id');
    }

    /**
     * Get the wallet transaction associated with this order.
     */
    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id');
    }

    /**
     * Check if this order used wallet payment (full or partial).
     */
    public function usedWallet(): bool
    {
        return $this->wallet_amount > 0 || str_starts_with($this->payment_method ?? '', 'wallet');
    }

    /**
     * Check if this was a partial wallet payment.
     */
    public function isPartialWalletPayment(): bool
    {
        return $this->wallet_amount > 0 && $this->secondary_payment_method !== null;
    }

    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
