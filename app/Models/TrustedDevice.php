<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrustedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'last_used_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique device ID.
     */
    public static function generateDeviceId(Request $request): string
    {
        $components = [
            $request->userAgent() ?? '',
            $request->ip(),
            $request->header('Accept-Language', ''),
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Check if current device is trusted.
     */
    public static function isTrusted(int $userId, Request $request): bool
    {
        $deviceId = self::generateDeviceId($request);

        return self::where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Trust current device.
     */
    public static function trustDevice(int $userId, Request $request, ?string $name = null): self
    {
        $deviceId = self::generateDeviceId($request);
        $userAgent = $request->userAgent() ?? '';

        // Parse user agent for device info
        $browser = self::detectBrowser($userAgent);
        $platform = self::detectPlatform($userAgent);
        $deviceType = self::detectDeviceType($userAgent);

        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'device_id' => $deviceId,
            ],
            [
                'device_name' => $name ?? "{$browser} on {$platform}",
                'device_type' => $deviceType,
                'browser' => $browser,
                'platform' => $platform,
                'ip_address' => $request->ip(),
                'last_used_at' => now(),
                'is_active' => true,
            ]
        );
    }

    /**
     * Update last used time.
     */
    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Revoke device trust.
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Detect browser from user agent.
     */
    protected static function detectBrowser(string $userAgent): string
    {
        $browsers = [
            'Edge' => '/Edge|Edg/i',
            'Chrome' => '/Chrome/i',
            'Firefox' => '/Firefox/i',
            'Safari' => '/Safari/i',
            'Opera' => '/Opera|OPR/i',
            'IE' => '/MSIE|Trident/i',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }

    /**
     * Detect platform from user agent.
     */
    protected static function detectPlatform(string $userAgent): string
    {
        $platforms = [
            'Windows' => '/Windows/i',
            'macOS' => '/Macintosh|Mac OS/i',
            'Linux' => '/Linux/i',
            'iOS' => '/iPhone|iPad|iPod/i',
            'Android' => '/Android/i',
        ];

        foreach ($platforms as $platform => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $platform;
            }
        }

        return 'Unknown';
    }

    /**
     * Detect device type from user agent.
     */
    protected static function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android.*Mobile|iPhone|iPod/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/iPad|Android(?!.*Mobile)|Tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
