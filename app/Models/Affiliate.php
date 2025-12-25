<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'paypal_email',
        'commission_rate',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_referrals',
        'successful_referrals',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'pending_earnings' => 'decimal:2',
            'paid_earnings' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->code)) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (self::where('code', $code)->exists());
                $affiliate->code = $code;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function getReferralUrl(): string
    {
        return url('/?ref=' . $this->code);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function addEarnings(float $amount): void
    {
        $this->increment('pending_earnings', $amount);
        $this->increment('total_earnings', $amount);
    }

    public function markAsPaid(float $amount): void
    {
        $this->decrement('pending_earnings', $amount);
        $this->increment('paid_earnings', $amount);
    }
}
