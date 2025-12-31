<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'escrow_transaction_id',
        'disputable_type',
        'disputable_id',
        'initiated_by',
        'reason',
        'description',
        'evidence',
        'status',
        'resolution_type',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(EscrowTransaction::class);
    }

    public function disputable(): MorphTo
    {
        return $this->morphTo();
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // Status checks
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function canAddEvidence(): bool
    {
        return in_array($this->status, ['open', 'under_review']);
    }

    public function canResolve(): bool
    {
        return in_array($this->status, ['open', 'under_review']);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? ucfirst(str_replace('_', ' ', $this->reason));
    }

    public const STATUSES = [
        'open' => 'Open',
        'under_review' => 'Under Review',
        'resolved' => 'Resolved',
        'cancelled' => 'Cancelled',
    ];

    public const REASONS = [
        'not_as_described' => 'Not as Described',
        'incomplete_delivery' => 'Incomplete Delivery',
        'poor_quality' => 'Poor Quality',
        'late_delivery' => 'Late Delivery',
        'communication_issues' => 'Communication Issues',
        'seller_unresponsive' => 'Seller Unresponsive',
        'buyer_unresponsive' => 'Buyer Unresponsive',
        'payment_issue' => 'Payment Issue',
        'scope_change' => 'Scope Change',
        'other' => 'Other',
    ];

    public const RESOLUTION_TYPES = [
        'full_refund' => 'Full Refund to Buyer',
        'partial_refund' => 'Partial Refund',
        'release_to_seller' => 'Release to Seller',
        'split' => 'Split Between Parties',
        'mutual_cancellation' => 'Mutual Cancellation',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    public static function getReasons(): array
    {
        return self::REASONS;
    }

    public static function getResolutionTypes(): array
    {
        return self::RESOLUTION_TYPES;
    }
}
