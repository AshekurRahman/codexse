<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'exchange_rate',
        'decimal_separator',
        'thousand_separator',
        'decimal_places',
        'is_default',
        'is_active',
        'rate_updated_at',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'decimal_places' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'rate_updated_at' => 'datetime',
    ];

    /**
     * Get the default currency.
     */
    public static function getDefault(): ?self
    {
        return Cache::remember('currency_default', 3600, function () {
            return static::where('is_default', true)->first()
                ?? static::where('code', 'USD')->first()
                ?? static::first();
        });
    }

    /**
     * Get all active currencies.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('currencies_active', 3600, function () {
            return static::where('is_active', true)->orderBy('name')->get();
        });
    }

    /**
     * Get currency by code.
     */
    public static function findByCode(string $code): ?self
    {
        return Cache::remember("currency_{$code}", 3600, function () use ($code) {
            return static::where('code', strtoupper($code))->first();
        });
    }

    /**
     * Format a price in this currency.
     */
    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousand_separator
        );

        if ($this->symbol_position === 'after') {
            return $formatted . ' ' . $this->symbol;
        }

        return $this->symbol . $formatted;
    }

    /**
     * Convert amount from base currency to this currency.
     */
    public function convertFromBase(float $amount): float
    {
        return round($amount * $this->exchange_rate, $this->decimal_places);
    }

    /**
     * Convert amount from this currency to base currency.
     */
    public function convertToBase(float $amount): float
    {
        if ($this->exchange_rate == 0) {
            return $amount;
        }
        return round($amount / $this->exchange_rate, 2);
    }

    /**
     * Convert amount from this currency to another currency.
     */
    public function convertTo(float $amount, Currency $targetCurrency): float
    {
        // First convert to base currency, then to target
        $baseAmount = $this->convertToBase($amount);
        return $targetCurrency->convertFromBase($baseAmount);
    }

    /**
     * Set this currency as default.
     */
    public function setAsDefault(): void
    {
        static::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
        Cache::forget('currency_default');
    }

    /**
     * Clear all currency caches.
     */
    public static function clearCache(): void
    {
        Cache::forget('currency_default');
        Cache::forget('currencies_active');

        foreach (static::pluck('code') as $code) {
            Cache::forget("currency_{$code}");
        }
    }

    /**
     * Scope: Active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot method to clear cache on model events.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Common currencies data for seeding.
     */
    public static function getCommonCurrencies(): array
    {
        return [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'symbol_position' => 'before', 'exchange_rate' => 1.000000, 'is_default' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'symbol_position' => 'before', 'exchange_rate' => 0.920000],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'symbol_position' => 'before', 'exchange_rate' => 0.790000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'symbol_position' => 'before', 'exchange_rate' => 1.360000],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'symbol_position' => 'before', 'exchange_rate' => 1.530000],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'symbol_position' => 'before', 'exchange_rate' => 149.500000, 'decimal_places' => 0],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'symbol_position' => 'before', 'exchange_rate' => 7.240000],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'symbol_position' => 'before', 'exchange_rate' => 83.200000],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'symbol_position' => 'before', 'exchange_rate' => 4.970000],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => 'MX$', 'symbol_position' => 'before', 'exchange_rate' => 17.150000],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'symbol_position' => 'before', 'exchange_rate' => 0.880000],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr', 'symbol_position' => 'after', 'exchange_rate' => 10.450000],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'symbol_position' => 'before', 'exchange_rate' => 1.640000],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'symbol_position' => 'before', 'exchange_rate' => 1.340000],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'symbol_position' => 'before', 'exchange_rate' => 7.820000],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩', 'symbol_position' => 'before', 'exchange_rate' => 1320.000000, 'decimal_places' => 0],
            ['code' => 'PLN', 'name' => 'Polish Złoty', 'symbol' => 'zł', 'symbol_position' => 'after', 'exchange_rate' => 4.020000],
            ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿', 'symbol_position' => 'before', 'exchange_rate' => 35.500000],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'symbol_position' => 'before', 'exchange_rate' => 3.670000],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'symbol_position' => 'before', 'exchange_rate' => 3.750000],
        ];
    }
}
