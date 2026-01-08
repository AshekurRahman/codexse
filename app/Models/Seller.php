<?php

namespace App\Models;

use App\Filament\Admin\Pages\CommissionSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Seller extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'categories',
        'other_category',
        'logo',
        'banner',
        'website',
        'stripe_account_id',
        'stripe_onboarding_complete',
        'commission_rate',
        'status',
        'level',
        'is_verified',
        'verification_status',
        'verified_at',
        'verification_badges',
        'is_featured',
        'is_on_vacation',
        'vacation_message',
        'vacation_started_at',
        'vacation_ends_at',
        'vacation_auto_reply',
        'total_sales',
        'total_earnings',
        'available_balance',
        'products_count',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'stripe_onboarding_complete' => 'boolean',
            'is_verified' => 'boolean',
            'verification_badges' => 'array',
            'verified_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_on_vacation' => 'boolean',
            'vacation_auto_reply' => 'boolean',
            'vacation_started_at' => 'datetime',
            'vacation_ends_at' => 'datetime',
            'commission_rate' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'available_balance' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('store_name')
            ->saveSlugsTo('store_slug');
    }

    public function getRouteKeyName(): string
    {
        return 'store_slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get balance attribute (alias for available_balance)
     */
    public function getBalanceAttribute(): float
    {
        return (float) $this->available_balance;
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(ProductBundle::class);
    }

    public function followers(): HasMany
    {
        return $this->hasMany(SellerFollow::class);
    }

    /**
     * Get the followers count attribute.
     */
    public function getFollowersCountAttribute(): int
    {
        // Use the loaded relationship count if available, otherwise query
        if (array_key_exists('followers_count', $this->attributes)) {
            return (int) $this->attributes['followers_count'];
        }

        return $this->followers()->count();
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function jobProposals(): HasMany
    {
        return $this->hasMany(JobProposal::class);
    }

    public function jobContracts(): HasMany
    {
        return $this->hasMany(JobContract::class);
    }

    public function escrowTransactions(): HasMany
    {
        return $this->hasMany(EscrowTransaction::class);
    }

    public function subscriptionPlans(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->whereIn('status', ['active', 'trialing']);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(SellerVerification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(SellerVerification::class)->latestOfMany();
    }

    public function pendingVerification()
    {
        return $this->hasOne(SellerVerification::class)->where('status', 'pending')->latestOfMany();
    }

    /**
     * Check if seller has a specific verification badge.
     */
    public function hasVerificationBadge(string $type): bool
    {
        $badges = $this->verification_badges ?? [];
        return isset($badges[$type]);
    }

    /**
     * Get all verification badge types the seller has earned.
     */
    public function getEarnedBadgesAttribute(): array
    {
        return array_keys($this->verification_badges ?? []);
    }

    /**
     * Check if seller can request verification.
     */
    public function canRequestVerification(): bool
    {
        // Must be approved seller
        if ($this->status !== 'approved') {
            return false;
        }

        // Check if there's a pending verification
        $pendingCount = $this->verifications()
            ->whereIn('status', ['pending', 'under_review'])
            ->count();

        return $pendingCount === 0;
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        // Fall back to user's avatar if available
        if ($this->user) {
            if ($this->user->avatar) {
                return asset('storage/' . $this->user->avatar);
            }
            if ($this->user->social_avatar) {
                return $this->user->social_avatar;
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->store_name) . '&background=6366f1&color=fff';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get the effective commission rate for this seller.
     * Returns the rate as a decimal (e.g., 0.20 for 20%).
     */
    public function getEffectiveCommissionRate(): float
    {
        return CommissionSettings::getCommissionRateForSeller($this);
    }

    /**
     * Get the effective commission rate as a percentage.
     */
    public function getEffectiveCommissionPercentAttribute(): float
    {
        return $this->getEffectiveCommissionRate() * 100;
    }

    /**
     * Get the seller's earnings percentage (100 - commission).
     */
    public function getEarningsPercentAttribute(): float
    {
        return 100 - $this->effective_commission_percent;
    }

    /**
     * Calculate platform fee for a given amount.
     */
    public function calculatePlatformFee(float $amount): float
    {
        return round($amount * $this->getEffectiveCommissionRate(), 2);
    }

    /**
     * Calculate seller earnings for a given amount.
     */
    public function calculateSellerEarnings(float $amount): float
    {
        return round($amount - $this->calculatePlatformFee($amount), 2);
    }

    /**
     * Check if seller is currently on vacation
     */
    public function isOnVacation(): bool
    {
        if (!$this->is_on_vacation) {
            return false;
        }

        // Check if vacation has ended
        if ($this->vacation_ends_at && $this->vacation_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Enable vacation mode
     */
    public function enableVacationMode(?string $message = null, ?\DateTime $endsAt = null, bool $autoReply = true): void
    {
        $this->update([
            'is_on_vacation' => true,
            'vacation_message' => $message,
            'vacation_started_at' => now(),
            'vacation_ends_at' => $endsAt,
            'vacation_auto_reply' => $autoReply,
        ]);
    }

    /**
     * Disable vacation mode
     */
    public function disableVacationMode(): void
    {
        $this->update([
            'is_on_vacation' => false,
            'vacation_message' => null,
            'vacation_started_at' => null,
            'vacation_ends_at' => null,
        ]);
    }

    /**
     * Get vacation status display
     */
    public function getVacationStatusAttribute(): ?string
    {
        if (!$this->isOnVacation()) {
            return null;
        }

        if ($this->vacation_ends_at) {
            return 'On vacation until ' . $this->vacation_ends_at->format('M d, Y');
        }

        return 'Currently on vacation';
    }

    /**
     * Scope for active (not on vacation) sellers
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_on_vacation', false)
              ->orWhere(function ($q2) {
                  $q2->where('is_on_vacation', true)
                     ->whereNotNull('vacation_ends_at')
                     ->where('vacation_ends_at', '<', now());
              });
        });
    }

    /**
     * Scope for sellers on vacation
     */
    public function scopeOnVacation($query)
    {
        return $query->where('is_on_vacation', true)
            ->where(function ($q) {
                $q->whereNull('vacation_ends_at')
                  ->orWhere('vacation_ends_at', '>', now());
            });
    }
}
