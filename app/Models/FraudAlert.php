<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FraudAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_number',
        'user_id',
        'alertable_type',
        'alertable_id',
        'type',
        'severity',
        'status',
        'risk_score',
        'transaction_amount',
        'transaction_currency',
        'payment_method',
        'payment_id',
        'detection_rules',
        'detection_data',
        'description',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'country_code',
        'city',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'action_taken',
        'auto_blocked',
        'notification_sent',
    ];

    protected $casts = [
        'detection_rules' => 'array',
        'detection_data' => 'array',
        'risk_score' => 'decimal:2',
        'transaction_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'auto_blocked' => 'boolean',
        'notification_sent' => 'boolean',
    ];

    // Alert types
    public const TYPE_VELOCITY = 'velocity';
    public const TYPE_HIGH_AMOUNT = 'high_amount';
    public const TYPE_NEW_ACCOUNT = 'new_account';
    public const TYPE_FAILED_ATTEMPTS = 'failed_attempts';
    public const TYPE_GEO_ANOMALY = 'geo_anomaly';
    public const TYPE_DEVICE_ANOMALY = 'device_anomaly';
    public const TYPE_CARD_TESTING = 'card_testing';
    public const TYPE_MULTIPLE_CARDS = 'multiple_cards';
    public const TYPE_BLOCKED_IP = 'blocked_ip';
    public const TYPE_SUSPICIOUS_PATTERN = 'suspicious_pattern';
    public const TYPE_CHARGEBACK_HISTORY = 'chargeback_history';

    public const TYPES = [
        self::TYPE_VELOCITY => 'High Transaction Velocity',
        self::TYPE_HIGH_AMOUNT => 'Unusually High Amount',
        self::TYPE_NEW_ACCOUNT => 'New Account Risk',
        self::TYPE_FAILED_ATTEMPTS => 'Multiple Failed Attempts',
        self::TYPE_GEO_ANOMALY => 'Geographic Anomaly',
        self::TYPE_DEVICE_ANOMALY => 'Device Anomaly',
        self::TYPE_CARD_TESTING => 'Card Testing Pattern',
        self::TYPE_MULTIPLE_CARDS => 'Multiple Payment Methods',
        self::TYPE_BLOCKED_IP => 'Blocked IP Address',
        self::TYPE_SUSPICIOUS_PATTERN => 'Suspicious Pattern',
        self::TYPE_CHARGEBACK_HISTORY => 'Chargeback History',
    ];

    // Severities
    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    public const SEVERITIES = [
        self::SEVERITY_LOW => 'Low',
        self::SEVERITY_MEDIUM => 'Medium',
        self::SEVERITY_HIGH => 'High',
        self::SEVERITY_CRITICAL => 'Critical',
    ];

    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_CONFIRMED = 'confirmed_fraud';
    public const STATUS_FALSE_POSITIVE = 'false_positive';
    public const STATUS_RESOLVED = 'resolved';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending Review',
        self::STATUS_REVIEWING => 'Under Review',
        self::STATUS_CONFIRMED => 'Confirmed Fraud',
        self::STATUS_FALSE_POSITIVE => 'False Positive',
        self::STATUS_RESOLVED => 'Resolved',
    ];

    // Actions
    public const ACTION_NONE = 'none';
    public const ACTION_BLOCKED = 'blocked';
    public const ACTION_REFUNDED = 'refunded';
    public const ACTION_SUSPENDED = 'account_suspended';
    public const ACTION_BANNED = 'account_banned';

    public const ACTIONS = [
        self::ACTION_NONE => 'No Action',
        self::ACTION_BLOCKED => 'Transaction Blocked',
        self::ACTION_REFUNDED => 'Refunded',
        self::ACTION_SUSPENDED => 'Account Suspended',
        self::ACTION_BANNED => 'Account Banned',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alert) {
            if (empty($alert->alert_number)) {
                $alert->alert_number = 'FRD-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 70);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_REVIEWING]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Accessors
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getSeverityNameAttribute(): string
    {
        return self::SEVERITIES[$this->severity] ?? ucfirst($this->severity);
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            self::SEVERITY_LOW => 'info',
            self::SEVERITY_MEDIUM => 'warning',
            self::SEVERITY_HIGH => 'danger',
            self::SEVERITY_CRITICAL => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_REVIEWING => 'info',
            self::STATUS_CONFIRMED => 'danger',
            self::STATUS_FALSE_POSITIVE => 'success',
            self::STATUS_RESOLVED => 'success',
            default => 'secondary',
        };
    }

    public function getRiskLevelAttribute(): string
    {
        return match (true) {
            $this->risk_score >= 80 => 'Critical',
            $this->risk_score >= 60 => 'High',
            $this->risk_score >= 40 => 'Medium',
            default => 'Low',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        if (!$this->transaction_amount) {
            return 'N/A';
        }
        return '$' . number_format($this->transaction_amount, 2);
    }

    // Methods
    public function markAsReviewing(?User $reviewer = null): void
    {
        $this->update([
            'status' => self::STATUS_REVIEWING,
            'reviewed_by' => $reviewer?->id ?? auth()->id(),
        ]);
    }

    public function resolve(string $status, ?string $action = null, ?string $notes = null): void
    {
        $this->update([
            'status' => $status,
            'action_taken' => $action ?? self::ACTION_NONE,
            'review_notes' => $notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function confirmFraud(?string $action = null, ?string $notes = null): void
    {
        $this->resolve(self::STATUS_CONFIRMED, $action, $notes);
    }

    public function markAsFalsePositive(?string $notes = null): void
    {
        $this->resolve(self::STATUS_FALSE_POSITIVE, self::ACTION_NONE, $notes);
    }

    public function isHighRisk(): bool
    {
        return $this->risk_score >= 70 || $this->severity === self::SEVERITY_CRITICAL;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
