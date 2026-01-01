<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'seller_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ended_at',
        'paused_at',
        'resumes_at',
        'cancel_at_period_end',
        'downloads_used',
        'requests_used',
        'metadata',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ended_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumes_at' => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function usage(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trialing']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('current_period_end', '<=', now()->addDays($days))
            ->whereIn('status', ['active', 'trialing']);
    }

    // Status Checks
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled' || $this->cancel_at_period_end;
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->current_period_end && $this->current_period_end->isPast() && !$this->isActive());
    }

    public function hasEnded(): bool
    {
        return $this->ended_at !== null;
    }

    // Usage tracking
    public function canDownload(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $maxDownloads = $this->plan->max_downloads;
        if ($maxDownloads === null) {
            return true; // Unlimited
        }

        return $this->downloads_used < $maxDownloads;
    }

    public function canMakeRequest(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $maxRequests = $this->plan->max_requests;
        if ($maxRequests === null) {
            return true; // Unlimited
        }

        return $this->requests_used < $maxRequests;
    }

    public function recordDownload(): void
    {
        $this->increment('downloads_used');
        $this->usage()->create([
            'feature' => 'downloads',
            'quantity' => 1,
            'recorded_at' => now(),
        ]);
    }

    public function recordRequest(): void
    {
        $this->increment('requests_used');
        $this->usage()->create([
            'feature' => 'requests',
            'quantity' => 1,
            'recorded_at' => now(),
        ]);
    }

    public function resetUsage(): void
    {
        $this->update([
            'downloads_used' => 0,
            'requests_used' => 0,
        ]);
    }

    // Actions
    public function cancel(bool $immediately = false): void
    {
        if ($immediately) {
            $this->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'ended_at' => now(),
            ]);
        } else {
            $this->update([
                'cancel_at_period_end' => true,
                'canceled_at' => now(),
            ]);
        }
    }

    public function resume(): void
    {
        if ($this->cancel_at_period_end) {
            $this->update([
                'cancel_at_period_end' => false,
                'canceled_at' => null,
            ]);
        }

        if ($this->status === 'paused') {
            $this->update([
                'status' => 'active',
                'paused_at' => null,
                'resumes_at' => null,
            ]);
        }
    }

    public function pause(?Carbon $resumeAt = null): void
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
            'resumes_at' => $resumeAt,
        ]);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'trialing' => 'Trial',
            'past_due' => 'Past Due',
            'paused' => 'Paused',
            'canceled' => 'Canceled',
            'expired' => 'Expired',
            'incomplete' => 'Incomplete',
            'incomplete_expired' => 'Expired',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'trialing' => 'info',
            'past_due' => 'warning',
            'paused' => 'warning',
            'canceled', 'expired', 'incomplete_expired' => 'danger',
            'incomplete' => 'warning',
            default => 'secondary',
        };
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->current_period_end) {
            return null;
        }

        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    public function getTrialDaysRemainingAttribute(): ?int
    {
        if (!$this->isTrialing() || !$this->trial_ends_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    public function getDownloadsRemainingAttribute(): ?int
    {
        $max = $this->plan->max_downloads;
        if ($max === null) {
            return null; // Unlimited
        }

        return max(0, $max - $this->downloads_used);
    }

    public function getRequestsRemainingAttribute(): ?int
    {
        $max = $this->plan->max_requests;
        if ($max === null) {
            return null; // Unlimited
        }

        return max(0, $max - $this->requests_used);
    }

    // Status options
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'trialing' => 'Trialing',
            'past_due' => 'Past Due',
            'paused' => 'Paused',
            'canceled' => 'Canceled',
            'expired' => 'Expired',
            'incomplete' => 'Incomplete',
            'incomplete_expired' => 'Incomplete Expired',
        ];
    }
}
