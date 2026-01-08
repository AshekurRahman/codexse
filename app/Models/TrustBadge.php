<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TrustBadge extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'icon_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active badges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get trust badges for homepage display.
     */
    public static function getForHomepage(): Collection
    {
        return Cache::remember('homepage_trust_badges', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Clear cache when badge is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage_trust_badges'));
        static::deleted(fn () => Cache::forget('homepage_trust_badges'));
    }
}
