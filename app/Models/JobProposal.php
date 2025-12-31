<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'seller_id',
        'cover_letter',
        'proposed_price',
        'proposed_duration',
        'duration_type',
        'milestones',
        'attachments',
        'status',
        'withdrawn_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'proposed_price' => 'decimal:2',
        'milestones' => 'array',
        'attachments' => 'array',
        'withdrawn_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(JobContract::class, 'proposal_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    // Status checks
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function canWithdraw(): bool
    {
        return $this->status === 'pending';
    }

    public function canAccept(): bool
    {
        return $this->status === 'pending' && $this->jobPosting->isOpen();
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->proposed_price, 2);
    }

    public function getDurationTextAttribute(): ?string
    {
        if (!$this->proposed_duration) {
            return null;
        }

        $unit = match ($this->duration_type) {
            'days' => $this->proposed_duration == 1 ? 'day' : 'days',
            'weeks' => $this->proposed_duration == 1 ? 'week' : 'weeks',
            'months' => $this->proposed_duration == 1 ? 'month' : 'months',
            default => $this->duration_type ?? 'days',
        };

        return $this->proposed_duration . ' ' . $unit;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'shortlisted' => 'Shortlisted',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
