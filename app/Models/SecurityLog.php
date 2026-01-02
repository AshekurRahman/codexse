<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_id',
        'event_type',
        'severity',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const EVENT_TYPES = [
        'attack_detected' => 'Attack Detected',
        'login_failed' => 'Login Failed',
        'brute_force' => 'Brute Force Attempt',
        'ip_blocked' => 'IP Blocked',
        'suspicious_activity' => 'Suspicious Activity',
        'password_reset' => 'Password Reset',
        'account_locked' => 'Account Locked',
        'two_factor_failed' => '2FA Failed',
        'permission_denied' => 'Permission Denied',
        'rate_limited' => 'Rate Limited',
        'file_upload_blocked' => 'File Upload Blocked',
        'csrf_failure' => 'CSRF Failure',
    ];

    public const SEVERITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeByIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public static function log(
        string $eventType,
        string $description,
        string $severity = 'medium',
        ?string $ip = null,
        ?int $userId = null,
        array $metadata = []
    ): self {
        return self::create([
            'ip_address' => $ip ?? request()->ip(),
            'user_id' => $userId ?? auth()->id(),
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
