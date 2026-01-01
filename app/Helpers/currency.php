<?php

use App\Models\Currency;
use App\Services\CurrencyService;

if (!function_exists('currency')) {
    /**
     * Get the currency service instance.
     */
    function currency(): CurrencyService
    {
        return app(CurrencyService::class);
    }
}

if (!function_exists('format_price')) {
    /**
     * Format a price in user's currency.
     * Converts from base currency (USD) to user's selected currency.
     */
    function format_price(float $amount, ?string $currencyCode = null): string
    {
        return currency()->format($amount, $currencyCode);
    }
}

if (!function_exists('convert_price')) {
    /**
     * Convert a price from base currency to target currency.
     */
    function convert_price(float $amount, ?string $toCurrencyCode = null): float
    {
        return currency()->convert($amount, $toCurrencyCode);
    }
}

if (!function_exists('current_currency')) {
    /**
     * Get the current currency object.
     */
    function current_currency(): Currency
    {
        return currency()->getCurrentCurrency();
    }
}

if (!function_exists('current_currency_code')) {
    /**
     * Get the current currency code.
     */
    function current_currency_code(): string
    {
        return currency()->getCurrentCurrencyCode();
    }
}

if (!function_exists('current_currency_symbol')) {
    /**
     * Get the current currency symbol.
     */
    function current_currency_symbol(): string
    {
        return current_currency()->symbol;
    }
}

if (!function_exists('price_with_original')) {
    /**
     * Get formatted price with original in base currency.
     */
    function price_with_original(float $amount): array
    {
        return currency()->formatWithOriginal($amount);
    }
}
