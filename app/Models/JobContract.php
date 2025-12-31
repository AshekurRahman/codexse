<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'job_posting_id',
        'proposal_id',
        'client_id',
        'seller_id',
        'conversation_id',
        'title',
        'description',
        'total_amount',
        'platform_fee',
        'seller_amount',
        'payment_type',
        'status',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = 'JOB-' . strtoupper(Str::random(8));
            }
        });
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(JobProposal::class, 'proposal_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(JobMilestone::class)->orderBy('sort_order');
    }

    public function activeMilestone(): HasMany
    {
        return $this->hasMany(JobMilestone::class)
            ->whereIn('status', ['funded', 'in_progress', 'submitted']);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'disputable_id')
            ->where('disputable_type', self::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'active']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Status checks
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'active']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canAddMilestone(): bool
    {
        return in_array($this->status, ['pending', 'active']);
    }

    public function canComplete(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // All milestones must be completed or cancelled
        return !$this->milestones()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'active']);
    }

    public function canDispute(): bool
    {
        return $this->status === 'active';
    }

    // Calculated attributes
    public function getMilestonesProgressAttribute(): array
    {
        $total = $this->milestones()->count();
        $completed = $this->milestones()->where('status', 'completed')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->milestones()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalPendingAttribute(): float
    {
        return (float) $this->milestones()
            ->whereIn('status', ['pending', 'funded', 'in_progress', 'submitted'])
            ->sum('amount');
    }

    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public const STATUSES = [
        'pending' => 'Pending',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'disputed' => 'Disputed',
    ];

    public const PAYMENT_TYPES = [
        'fixed' => 'Fixed Price',
        'milestone' => 'Milestone-based',
        'hourly' => 'Hourly',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    public static function getPaymentTypes(): array
    {
        return self::PAYMENT_TYPES;
    }
}
