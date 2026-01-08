<?php

namespace App\Models;

use App\Notifications\PayoutApprovalRequest;
use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Notification;

class Payout extends Model
{
    use HasFactory;

    /**
     * Threshold amount (in dollars) above which payouts require approval.
     */
    public const APPROVAL_THRESHOLD = 500.00;

    /**
     * Status constants for payout workflow.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'seller_id',
        'amount',
        'currency',
        'status',
        'requires_approval',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'stripe_transfer_id',
        'stripe_payout_id',
        'notes',
        'failure_reason',
        'processed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requires_approval' => 'boolean',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if payout requires admin approval based on amount.
     */
    public static function requiresApprovalForAmount(float $amount): bool
    {
        return $amount > self::APPROVAL_THRESHOLD;
    }

    /**
     * Create a payout with automatic approval check.
     */
    public static function createWithApprovalCheck(array $attributes): self
    {
        $amount = $attributes['amount'] ?? 0;
        $requiresApproval = self::requiresApprovalForAmount($amount);

        $payout = self::create([
            ...$attributes,
            'requires_approval' => $requiresApproval,
            'status' => $requiresApproval ? self::STATUS_PENDING_APPROVAL : self::STATUS_PENDING,
        ]);

        if ($requiresApproval) {
            $payout->notifyAdminsForApproval();
        }

        return $payout;
    }

    /**
     * Notify admins about pending payout approval.
     */
    public function notifyAdminsForApproval(): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super_admin', 'admin', 'finance']);
        })->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new PayoutApprovalRequest($this));
        }
    }

    /**
     * Approve the payout.
     */
    public function approve(User $admin, ?string $notes = null): bool
    {
        if (!$this->isPendingApproval()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        ActivityLogService::logPayoutApproved($admin, $this, $notes);

        // Notify seller
        $this->seller->user->notify(new \App\Notifications\PayoutApproved($this));

        return true;
    }

    /**
     * Reject the payout.
     */
    public function reject(User $admin, string $reason): bool
    {
        if (!$this->isPendingApproval()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        ActivityLogService::logPayoutRejected($admin, $this, $reason);

        // Refund the amount back to seller's wallet
        $wallet = $this->seller->user->wallet;
        if ($wallet) {
            $wallet->credit(
                $this->amount,
                'payout_refund',
                "Payout #{$this->id} rejected: {$reason}",
                $this
            );
        }

        // Notify seller
        $this->seller->user->notify(new \App\Notifications\PayoutRejected($this));

        return true;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeProcessed(): bool
    {
        // Can process if pending or approved (for large payouts that went through approval)
        return $this->status === self::STATUS_PENDING || $this->status === self::STATUS_APPROVED;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRequiringAction($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED]);
    }
}
