<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HowItWorksStep extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'icon_color',
        'step_number',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active steps.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by step_number and sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number')->orderBy('sort_order');
    }

    /**
     * Get steps for homepage display.
     */
    public static function getForHomepage(): Collection
    {
        return Cache::remember('homepage_how_it_works', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Clear cache when step is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage_how_it_works'));
        static::deleted(fn () => Cache::forget('homepage_how_it_works'));
    }
}
