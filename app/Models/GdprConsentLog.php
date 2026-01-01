<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprConsentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'granted',
        'ip_address',
        'user_agent',
        'consent_text',
        'version',
        'granted_at',
        'revoked_at',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    // Consent types
    public const TYPE_NECESSARY = 'necessary';
    public const TYPE_MARKETING = 'marketing';
    public const TYPE_ANALYTICS = 'analytics';
    public const TYPE_THIRD_PARTY = 'third_party';
    public const TYPE_PRIVACY_POLICY = 'privacy_policy';
    public const TYPE_TERMS_OF_SERVICE = 'terms_of_service';

    public const TYPES = [
        self::TYPE_NECESSARY => 'Necessary Cookies',
        self::TYPE_MARKETING => 'Marketing Communications',
        self::TYPE_ANALYTICS => 'Analytics & Performance',
        self::TYPE_THIRD_PARTY => 'Third Party Services',
        self::TYPE_PRIVACY_POLICY => 'Privacy Policy',
        self::TYPE_TERMS_OF_SERVICE => 'Terms of Service',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeGranted($query)
    {
        return $query->where('granted', true)->whereNull('revoked_at');
    }

    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('consent_type', $type);
    }

    // Methods
    public static function recordConsent(
        User $user,
        string $consentType,
        bool $granted,
        ?string $consentText = null,
        ?string $version = null
    ): self {
        // Revoke any existing consent of the same type
        self::where('user_id', $user->id)
            ->where('consent_type', $consentType)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        return self::create([
            'user_id' => $user->id,
            'consent_type' => $consentType,
            'granted' => $granted,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'consent_text' => $consentText,
            'version' => $version,
            'granted_at' => $granted ? now() : null,
        ]);
    }

    public static function hasActiveConsent(User $user, string $consentType): bool
    {
        return self::where('user_id', $user->id)
            ->where('consent_type', $consentType)
            ->where('granted', true)
            ->whereNull('revoked_at')
            ->exists();
    }

    public static function getConsentHistory(User $user, ?string $consentType = null)
    {
        $query = self::where('user_id', $user->id)->orderBy('created_at', 'desc');

        if ($consentType) {
            $query->where('consent_type', $consentType);
        }

        return $query->get();
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->consent_type] ?? ucfirst(str_replace('_', ' ', $this->consent_type));
    }

    public function isActive(): bool
    {
        return $this->granted && $this->revoked_at === null;
    }
}
