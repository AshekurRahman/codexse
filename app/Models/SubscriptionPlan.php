<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'service_id',
        'seller_id',
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'billing_interval',
        'trial_days',
        'features',
        'max_downloads',
        'max_requests',
        'is_active',
        'is_featured',
        'sort_order',
        'stripe_price_id',
        'stripe_product_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name) . '-' . Str::random(6);
            }
        });
    }

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    // Accessors
    public function getBillingPeriodLabelAttribute(): string
    {
        $labels = [
            'weekly' => 'Week',
            'monthly' => 'Month',
            'quarterly' => 'Quarter',
            'yearly' => 'Year',
        ];

        $interval = $this->billing_interval;
        $period = $labels[$this->billing_period] ?? $this->billing_period;

        if ($interval > 1) {
            return $interval . ' ' . Str::plural($period);
        }

        return $period;
    }

    public function getFormattedPriceAttribute(): string
    {
        return format_price($this->price);
    }

    public function getPricePerMonthAttribute(): float
    {
        $monthlyMultiplier = match ($this->billing_period) {
            'weekly' => 4.33, // Average weeks in month
            'monthly' => 1,
            'quarterly' => 1 / 3,
            'yearly' => 1 / 12,
            default => 1,
        };

        return round($this->price * $monthlyMultiplier / $this->billing_interval, 2);
    }

    public function getTypeAttribute(): string
    {
        if ($this->product_id) {
            return 'product';
        }
        if ($this->service_id) {
            return 'service';
        }
        return 'standalone';
    }

    public function getItemAttribute()
    {
        return $this->product ?? $this->service;
    }

    // Methods
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getActiveSubscribersCount(): int
    {
        return $this->subscriptions()->whereIn('status', ['active', 'trialing'])->count();
    }

    public function canSubscribe(User $user): bool
    {
        // Check if user already has an active subscription to this plan
        return !$this->subscriptions()
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Billing period options
    public static function getBillingPeriodOptions(): array
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }
}
