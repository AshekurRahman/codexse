<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referred_user_id',
        'order_id',
        'type',
        'amount',
        'description',
        'status',
        'credited_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'credited_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function credit(): void
    {
        if ($this->status !== 'pending') {
            return;
        }

        $this->update([
            'status' => 'credited',
            'credited_at' => now(),
        ]);

        $this->user->increment('referral_balance', $this->amount);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCredited($query)
    {
        return $query->where('status', 'credited');
    }
}
