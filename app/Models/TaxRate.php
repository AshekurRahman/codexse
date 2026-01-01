<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_code',
        'state_code',
        'rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to get only active tax rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tax rate for a specific state.
     */
    public function scopeForState($query, string $stateCode)
    {
        return $query->where('state_code', strtoupper($stateCode))
            ->where('country_code', 'US');
    }

    /**
     * Get tax rate for a US state.
     */
    public static function getForState(?string $stateCode): ?self
    {
        if (empty($stateCode)) {
            return null;
        }

        return Cache::remember(
            "tax_rate_us_{$stateCode}",
            3600, // 1 hour
            fn () => static::active()->forState($stateCode)->first()
        );
    }

    /**
     * Calculate tax amount for a given subtotal.
     */
    public function calculateTax(float $amount): float
    {
        return round($amount * ($this->rate / 100), 2);
    }

    /**
     * Get formatted rate string (e.g., "7.25%").
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2) . '%';
    }

    /**
     * Get full display name (e.g., "California (7.25%)").
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->formatted_rate})";
    }

    /**
     * Clear the tax rate cache.
     */
    public static function clearCache(): void
    {
        $states = array_keys(config('tax.states', []));

        foreach ($states as $state) {
            Cache::forget("tax_rate_us_{$state}");
        }
    }

    /**
     * Boot method to clear cache on changes.
     */
    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
