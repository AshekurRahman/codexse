<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class JobMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_contract_id',
        'title',
        'description',
        'amount',
        'sort_order',
        'due_date',
        'status',
        'submission_notes',
        'submission_files',
        'revision_notes',
        'funded_at',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'submission_files' => 'array',
        'due_date' => 'date',
        'funded_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(JobContract::class, 'job_contract_id');
    }

    public function escrowTransaction(): MorphOne
    {
        return $this->morphOne(EscrowTransaction::class, 'escrowable');
    }

    // Status checks
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFunded(): bool
    {
        return $this->status === 'funded';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function canFund(): bool
    {
        return $this->status === 'pending';
    }

    public function canStart(): bool
    {
        return $this->status === 'funded';
    }

    public function canSubmit(): bool
    {
        return in_array($this->status, ['funded', 'in_progress', 'revision_requested']);
    }

    public function canApprove(): bool
    {
        return $this->status === 'submitted';
    }

    public function canRequestRevision(): bool
    {
        return $this->status === 'submitted';
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return format_price($this->amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public const STATUSES = [
        'pending' => 'Pending Funding',
        'funded' => 'Funded',
        'in_progress' => 'In Progress',
        'submitted' => 'Submitted',
        'revision_requested' => 'Revision Requested',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
