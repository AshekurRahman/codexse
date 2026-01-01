<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GdprDataRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'type',
        'status',
        'reason',
        'admin_notes',
        'export_file_path',
        'export_expires_at',
        'processed_by',
        'processed_at',
        'completed_at',
        'data_categories',
        'verification_data',
        'identity_verified',
    ];

    protected $casts = [
        'data_categories' => 'array',
        'verification_data' => 'array',
        'identity_verified' => 'boolean',
        'export_expires_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Request types
    public const TYPE_EXPORT = 'export';
    public const TYPE_DELETION = 'deletion';
    public const TYPE_RECTIFICATION = 'rectification';
    public const TYPE_RESTRICTION = 'restriction';

    public const TYPES = [
        self::TYPE_EXPORT => 'Data Export (Right to Access)',
        self::TYPE_DELETION => 'Data Deletion (Right to Erasure)',
        self::TYPE_RECTIFICATION => 'Data Rectification',
        self::TYPE_RESTRICTION => 'Processing Restriction',
    ];

    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending Review',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // Data categories that can be exported
    public const DATA_CATEGORIES = [
        'personal' => 'Personal Information',
        'account' => 'Account Details',
        'orders' => 'Order History',
        'transactions' => 'Financial Transactions',
        'communications' => 'Messages & Communications',
        'reviews' => 'Reviews & Feedback',
        'preferences' => 'Preferences & Settings',
        'activity' => 'Activity Logs',
        'downloads' => 'Download History',
        'subscriptions' => 'Subscriptions',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_number)) {
                $request->request_number = 'GDPR-' . strtoupper(uniqid());
            }
            if (empty($request->data_categories)) {
                $request->data_categories = array_keys(self::DATA_CATEGORIES);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeExportRequests($query)
    {
        return $query->where('type', self::TYPE_EXPORT);
    }

    public function scopeDeletionRequests($query)
    {
        return $query->where('type', self::TYPE_DELETION);
    }

    // Accessors
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'gray',
        };
    }

    public function getIsExportAvailableAttribute(): bool
    {
        return $this->type === self::TYPE_EXPORT
            && $this->status === self::STATUS_COMPLETED
            && $this->export_file_path
            && $this->export_expires_at
            && $this->export_expires_at->isFuture()
            && Storage::exists($this->export_file_path);
    }

    public function getExportDownloadUrlAttribute(): ?string
    {
        if (!$this->is_export_available) {
            return null;
        }
        return route('gdpr.download', $this);
    }

    // Methods
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        GdprProcessingLog::create([
            'user_id' => $this->user_id,
            'activity_type' => 'request_processing',
            'data_category' => 'gdpr',
            'description' => "GDPR {$this->type_name} request started processing",
            'performed_by' => auth()->id(),
            'metadata' => ['request_id' => $this->id],
        ]);
    }

    public function complete(?string $exportPath = null): void
    {
        $updateData = [
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ];

        if ($exportPath) {
            $updateData['export_file_path'] = $exportPath;
            $updateData['export_expires_at'] = now()->addDays(7); // Export available for 7 days
        }

        $this->update($updateData);

        GdprProcessingLog::create([
            'user_id' => $this->user_id,
            'activity_type' => 'request_completed',
            'data_category' => 'gdpr',
            'description' => "GDPR {$this->type_name} request completed",
            'performed_by' => auth()->id(),
            'metadata' => ['request_id' => $this->id],
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_notes' => $reason,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        GdprProcessingLog::create([
            'user_id' => $this->user_id,
            'activity_type' => 'request_rejected',
            'data_category' => 'gdpr',
            'description' => "GDPR {$this->type_name} request rejected: {$reason}",
            'performed_by' => auth()->id(),
            'metadata' => ['request_id' => $this->id, 'reason' => $reason],
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING]);
    }

    public function deleteExportFile(): void
    {
        if ($this->export_file_path && Storage::exists($this->export_file_path)) {
            Storage::delete($this->export_file_path);
            $this->update(['export_file_path' => null]);
        }
    }
}
