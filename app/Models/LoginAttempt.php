<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'user_id',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
        'country_code',
        'city',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    // Failure reasons
    public const REASON_INVALID_CREDENTIALS = 'invalid_credentials';
    public const REASON_ACCOUNT_DISABLED = 'account_disabled';
    public const REASON_EMAIL_NOT_VERIFIED = 'email_not_verified';
    public const REASON_TOO_MANY_ATTEMPTS = 'too_many_attempts';
    public const REASON_2FA_FAILED = '2fa_failed';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    // Methods
    public static function recordAttempt(
        string $email,
        bool $successful,
        ?int $userId = null,
        ?string $failureReason = null
    ): self {
        return self::create([
            'email' => $email,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }

    public static function getRecentFailedAttempts(string $email, int $minutes = 60): int
    {
        return self::forEmail($email)->failed()->recent($minutes)->count();
    }

    public static function getRecentFailedAttemptsForIp(string $ip, int $minutes = 60): int
    {
        return self::forIp($ip)->failed()->recent($minutes)->count();
    }
}
