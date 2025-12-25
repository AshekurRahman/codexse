<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'order_id',
        'ip_address',
        'order_amount',
        'commission_amount',
        'status',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'order_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'converted_at' => 'datetime',
        ];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsRegistered(User $user): void
    {
        $this->update([
            'referred_user_id' => $user->id,
            'status' => 'registered',
            'converted_at' => now(),
        ]);

        $this->affiliate->increment('total_referrals');
    }

    public function markAsPurchased(Order $order, float $commission): void
    {
        $this->update([
            'order_id' => $order->id,
            'order_amount' => $order->total_amount,
            'commission_amount' => $commission,
            'status' => 'purchased',
        ]);

        $this->affiliate->increment('successful_referrals');
        $this->affiliate->addEarnings($commission);
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
}
