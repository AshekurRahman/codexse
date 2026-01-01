<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'country_code',
        'city',
        'is_current',
        'last_active_at',
        'logged_out_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'last_active_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('logged_out_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public static function createSession(User $user): self
    {
        $userAgent = request()->userAgent();
        $deviceInfo = self::parseUserAgent($userAgent);
        $sessionId = session()->getId();

        // Mark all other sessions as not current
        self::where('user_id', $user->id)->update(['is_current' => false]);

        return self::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'device_type' => $deviceInfo['device'],
                'browser' => $deviceInfo['browser'],
                'platform' => $deviceInfo['platform'],
                'is_current' => true,
                'last_active_at' => now(),
                'logged_out_at' => null,
            ]
        );
    }

    public static function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return ['device' => 'unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        // Device type detection
        $device = 'desktop';
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            $device = preg_match('/ipad|tablet/i', $userAgent) ? 'tablet' : 'mobile';
        }

        // Browser detection
        $browser = 'Unknown';
        if (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Platform detection
        $platform = 'Unknown';
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $platform = 'iOS';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        }

        return compact('device', 'browser', 'platform');
    }

    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    public function logout(): void
    {
        $this->update([
            'is_current' => false,
            'logged_out_at' => now(),
        ]);
    }

    public function revoke(): void
    {
        $this->logout();
    }

    public function isActive(): bool
    {
        return $this->logged_out_at === null;
    }

    public function getDeviceInfoAttribute(): string
    {
        $parts = array_filter([
            $this->device_type ? ucfirst($this->device_type) : null,
            $this->browser,
            $this->platform,
        ]);
        return implode(' / ', $parts) ?: 'Unknown Device';
    }

    public function getLocationAttribute(): ?string
    {
        if ($this->city && $this->country_code) {
            return "{$this->city}, {$this->country_code}";
        }
        return $this->country_code ?? $this->city;
    }
}
