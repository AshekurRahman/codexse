<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlockedIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'is_range',
        'reason',
        'blocked_by',
        'is_active',
        'expires_at',
        'blocked_requests_count',
    ];

    protected function casts(): array
    {
        return [
            'is_range' => 'boolean',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($blockedIp) {
            // Clear cache when IP is blocked/unblocked
            Cache::forget("blocked_ip:{$blockedIp->ip_address}");
        });

        static::deleted(function ($blockedIp) {
            Cache::forget("blocked_ip:{$blockedIp->ip_address}");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function incrementBlockedCount(): void
    {
        $this->increment('blocked_requests_count');
    }

    public static function block(
        string $ip,
        string $reason,
        string $blockedBy = 'manual',
        ?int $hours = null
    ): self {
        return self::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_by' => $blockedBy,
                'is_active' => true,
                'expires_at' => $hours ? now()->addHours($hours) : null,
            ]
        );
    }

    public static function unblock(string $ip): bool
    {
        $blocked = self::where('ip_address', $ip)->first();

        if ($blocked) {
            $blocked->update(['is_active' => false]);
            return true;
        }

        return false;
    }

    public static function isBlocked(string $ip): bool
    {
        return Cache::remember("blocked_ip:{$ip}", 300, function () use ($ip) {
            return self::where('ip_address', $ip)->active()->exists();
        });
    }
}
