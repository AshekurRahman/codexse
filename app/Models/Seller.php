<?php

namespace App\Models;

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
        'logo',
        'banner',
        'website',
        'stripe_account_id',
        'stripe_onboarding_complete',
        'commission_rate',
        'status',
        'level',
        'is_verified',
        'is_featured',
        'total_sales',
        'total_earnings',
        'available_balance',
        'products_count',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'stripe_onboarding_complete' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
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

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->store_name) . '&background=6366f1&color=fff';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
