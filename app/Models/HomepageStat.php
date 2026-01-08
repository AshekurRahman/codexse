<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomepageStat extends Model
{
    protected $fillable = [
        'label',
        'value',
        'prefix',
        'suffix',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active stats.
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
     * Get the formatted display value with prefix and suffix.
     */
    public function getDisplayValueAttribute(): string
    {
        return ($this->prefix ?? '') . $this->value . ($this->suffix ?? '');
    }

    /**
     * Get homepage stats for display.
     */
    public static function getForHomepage(): Collection
    {
        return Cache::remember('homepage_stats', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Clear cache when stat is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage_stats'));
        static::deleted(fn () => Cache::forget('homepage_stats'));
    }
}
