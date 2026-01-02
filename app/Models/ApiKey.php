<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'secret_hash',
        'user_id',
        'permissions',
        'allowed_ips',
        'rate_limit',
        'requests_count',
        'is_active',
        'last_used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'allowed_ips' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected $hidden = [
        'secret_hash',
    ];

    public const PERMISSIONS = [
        'read:products' => 'Read Products',
        'write:products' => 'Write Products',
        'read:orders' => 'Read Orders',
        'write:orders' => 'Write Orders',
        'read:users' => 'Read Users',
        'read:analytics' => 'Read Analytics',
    ];

    public static function generateKey(): string
    {
        return 'pk_' . Str::random(32);
    }

    public static function generateSecret(): string
    {
        return 'sk_' . Str::random(48);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return true; // No restrictions means all permissions
        }

        return in_array($permission, $this->permissions);
    }

    public function isIpAllowed(?string $ip): bool
    {
        if (empty($this->allowed_ips) || !$ip) {
            return true; // No restrictions
        }

        return in_array($ip, $this->allowed_ips);
    }

    public function recordUsage(): void
    {
        $this->increment('requests_count');
        $this->update(['last_used_at' => now()]);
    }

    public function isRateLimited(): bool
    {
        // Simple daily rate limit check
        $todayRequests = static::where('id', $this->id)
            ->where('last_used_at', '>=', now()->startOfDay())
            ->value('requests_count');

        return $todayRequests >= $this->rate_limit;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'expired' => 'warning',
            default => 'secondary',
        };
    }
}
