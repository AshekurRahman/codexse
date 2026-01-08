<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomepageSection extends Model
{
    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'badge_text',
        'description',
        'content',
        'metadata',
        'image',
        'button_text',
        'button_url',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get a specific section by key with caching.
     */
    public static function getSection(string $key): ?self
    {
        return Cache::remember("homepage_section_{$key}", 3600, function () use ($key) {
            return static::where('section_key', $key)->active()->first();
        });
    }

    /**
     * Get a metadata value from a section.
     */
    public static function getMetadata(string $key, string $metaKey, mixed $default = null): mixed
    {
        $section = static::getSection($key);

        return $section?->metadata[$metaKey] ?? $default;
    }

    /**
     * Get image URL with fallback.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Clear cache when section is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function ($section) {
            Cache::forget("homepage_section_{$section->section_key}");
        });

        static::deleted(function ($section) {
            Cache::forget("homepage_section_{$section->section_key}");
        });
    }
}
