<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprProcessingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'data_category',
        'description',
        'ip_address',
        'user_agent',
        'performed_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Activity types
    public const ACTIVITY_DATA_ACCESS = 'data_access';
    public const ACTIVITY_DATA_UPDATE = 'data_update';
    public const ACTIVITY_DATA_DELETION = 'data_deletion';
    public const ACTIVITY_DATA_EXPORT = 'data_export';
    public const ACTIVITY_CONSENT_GRANTED = 'consent_granted';
    public const ACTIVITY_CONSENT_REVOKED = 'consent_revoked';
    public const ACTIVITY_REQUEST_SUBMITTED = 'request_submitted';
    public const ACTIVITY_REQUEST_PROCESSING = 'request_processing';
    public const ACTIVITY_REQUEST_COMPLETED = 'request_completed';
    public const ACTIVITY_REQUEST_REJECTED = 'request_rejected';

    public const ACTIVITY_TYPES = [
        self::ACTIVITY_DATA_ACCESS => 'Data Accessed',
        self::ACTIVITY_DATA_UPDATE => 'Data Updated',
        self::ACTIVITY_DATA_DELETION => 'Data Deleted',
        self::ACTIVITY_DATA_EXPORT => 'Data Exported',
        self::ACTIVITY_CONSENT_GRANTED => 'Consent Granted',
        self::ACTIVITY_CONSENT_REVOKED => 'Consent Revoked',
        self::ACTIVITY_REQUEST_SUBMITTED => 'Request Submitted',
        self::ACTIVITY_REQUEST_PROCESSING => 'Request Processing',
        self::ACTIVITY_REQUEST_COMPLETED => 'Request Completed',
        self::ACTIVITY_REQUEST_REJECTED => 'Request Rejected',
    ];

    // Data categories
    public const CATEGORY_PERSONAL = 'personal';
    public const CATEGORY_FINANCIAL = 'financial';
    public const CATEGORY_USAGE = 'usage';
    public const CATEGORY_PREFERENCES = 'preferences';
    public const CATEGORY_COMMUNICATIONS = 'communications';
    public const CATEGORY_GDPR = 'gdpr';

    public const DATA_CATEGORIES = [
        self::CATEGORY_PERSONAL => 'Personal Information',
        self::CATEGORY_FINANCIAL => 'Financial Data',
        self::CATEGORY_USAGE => 'Usage Data',
        self::CATEGORY_PREFERENCES => 'Preferences',
        self::CATEGORY_COMMUNICATIONS => 'Communications',
        self::CATEGORY_GDPR => 'GDPR Request',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->ip_address)) {
                $log->ip_address = request()->ip();
            }
            if (empty($log->user_agent)) {
                $log->user_agent = request()->userAgent();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForActivity($query, string $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('data_category', $category);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public static function log(
        ?int $userId,
        string $activityType,
        string $dataCategory,
        string $description,
        ?int $performedBy = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'data_category' => $dataCategory,
            'description' => $description,
            'performed_by' => $performedBy ?? auth()->id(),
            'metadata' => $metadata,
        ]);
    }

    public function getActivityTypeNameAttribute(): string
    {
        return self::ACTIVITY_TYPES[$this->activity_type] ?? ucfirst(str_replace('_', ' ', $this->activity_type));
    }

    public function getDataCategoryNameAttribute(): string
    {
        return self::DATA_CATEGORIES[$this->data_category] ?? ucfirst(str_replace('_', ' ', $this->data_category));
    }
}
