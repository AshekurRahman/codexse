<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'company',
        'content',
        'rating',
        'avatar',
        'status',
        'is_featured',
        'sort_order',
        'user_id',
        'admin_notes',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only approved testimonials.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to order by sort_order and created_at.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    /**
     * Scope to get pending testimonials.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the user that submitted the testimonial.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the avatar URL with fallback to UI Avatars.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate UI Avatars fallback
        $name = urlencode($this->name);
        $colors = ['6366f1', '06b6d4', '10b981', 'f59e0b', 'ec4899', '8b5cf6'];
        $color = $colors[$this->id % count($colors)];

        return "https://ui-avatars.com/api/?name={$name}&background={$color}&color=fff&size=200";
    }

    /**
     * Get testimonials for homepage display.
     */
    public static function getForHomepage(int $limit = 6): Collection
    {
        return Cache::remember('homepage_testimonials', 3600, function () use ($limit) {
            return static::approved()
                ->featured()
                ->ordered()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Clear cache when testimonial is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('homepage_testimonials');
        });

        static::deleted(function () {
            Cache::forget('homepage_testimonials');
        });
    }
}
