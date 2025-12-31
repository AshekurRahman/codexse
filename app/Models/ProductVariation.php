<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'description',
        'price',
        'regular_price',
        'features',
        'downloads_limit',
        'support_months',
        'updates_months',
        'license_type',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'features' => 'array',
        'downloads_limit' => 'integer',
        'support_months' => 'integer',
        'updates_months' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variation) {
            if (empty($variation->slug)) {
                $variation->slug = Str::slug($variation->name);
            }
        });

        static::saving(function ($variation) {
            // If this is set as default, unset other defaults for the same product
            if ($variation->is_default) {
                static::where('product_id', $variation->product_id)
                    ->where('id', '!=', $variation->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->regular_price && $this->regular_price > $this->price) {
            return round((($this->regular_price - $this->price) / $this->regular_price) * 100);
        }
        return null;
    }

    public function getSavingsAttribute(): ?float
    {
        if ($this->regular_price && $this->regular_price > $this->price) {
            return $this->regular_price - $this->price;
        }
        return null;
    }

    public function getSupportExpiryAttribute(): ?string
    {
        if ($this->support_months > 0) {
            return now()->addMonths($this->support_months)->format('M Y');
        }
        return 'Lifetime';
    }

    public function getUpdatesExpiryAttribute(): ?string
    {
        if ($this->updates_months > 0) {
            return now()->addMonths($this->updates_months)->format('M Y');
        }
        return 'Lifetime';
    }
}
