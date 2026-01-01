<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    protected ?Currency $currentCurrency = null;
    protected ?Currency $baseCurrency = null;

    /**
     * Get the current user's currency.
     */
    public function getCurrentCurrency(): Currency
    {
        if ($this->currentCurrency) {
            return $this->currentCurrency;
        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $this->currentCurrency = Currency::findByCode($currencyCode) ?? Currency::getDefault();

        return $this->currentCurrency;
    }

    /**
     * Get current currency code from session, user preference, or default.
     */
    public function getCurrentCurrencyCode(): string
    {
        // First check session (guest users)
        if (Session::has('currency_code')) {
            return Session::get('currency_code');
        }

        // Then check authenticated user preference
        if (auth()->check() && auth()->user()->currency_code) {
            return auth()->user()->currency_code;
        }

        // Finally use default
        return Currency::getDefault()?->code ?? 'USD';
    }

    /**
     * Set the current currency.
     */
    public function setCurrency(string $currencyCode): bool
    {
        $currency = Currency::findByCode($currencyCode);

        if (!$currency || !$currency->is_active) {
            return false;
        }

        // Store in session for guests
        Session::put('currency_code', $currency->code);

        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['currency_code' => $currency->code]);
        }

        $this->currentCurrency = $currency;

        return true;
    }

    /**
     * Get the base currency (for storing prices).
     */
    public function getBaseCurrency(): Currency
    {
        if ($this->baseCurrency) {
            return $this->baseCurrency;
        }

        $this->baseCurrency = Currency::getDefault() ?? Currency::findByCode('USD');

        return $this->baseCurrency;
    }

    /**
     * Convert amount from base currency to user's currency.
     */
    public function convert(float $amount, ?string $toCurrencyCode = null): float
    {
        $targetCurrency = $toCurrencyCode
            ? Currency::findByCode($toCurrencyCode)
            : $this->getCurrentCurrency();

        if (!$targetCurrency) {
            return $amount;
        }

        return $targetCurrency->convertFromBase($amount);
    }

    /**
     * Format a price in user's currency.
     */
    public function format(float $amount, ?string $currencyCode = null): string
    {
        $currency = $currencyCode
            ? Currency::findByCode($currencyCode)
            : $this->getCurrentCurrency();

        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        // Convert from base currency if needed
        $convertedAmount = $currency->convertFromBase($amount);

        return $currency->format($convertedAmount);
    }

    /**
     * Format a price without conversion (already in target currency).
     */
    public function formatOnly(float $amount, ?string $currencyCode = null): string
    {
        $currency = $currencyCode
            ? Currency::findByCode($currencyCode)
            : $this->getCurrentCurrency();

        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        return $currency->format($amount);
    }

    /**
     * Get price display with original price (for checkout).
     */
    public function formatWithOriginal(float $baseAmount): array
    {
        $baseCurrency = $this->getBaseCurrency();
        $userCurrency = $this->getCurrentCurrency();

        $baseFormatted = $baseCurrency->format($baseAmount);

        if ($baseCurrency->code === $userCurrency->code) {
            return [
                'display' => $baseFormatted,
                'converted' => null,
                'original' => null,
            ];
        }

        $convertedAmount = $userCurrency->convertFromBase($baseAmount);
        $convertedFormatted = $userCurrency->format($convertedAmount);

        return [
            'display' => $convertedFormatted,
            'converted' => $convertedAmount,
            'original' => $baseFormatted,
        ];
    }

    /**
     * Update exchange rates from external API.
     */
    public function updateExchangeRates(): bool
    {
        $apiKey = Setting::get('exchange_rate_api_key');
        $baseCurrency = $this->getBaseCurrency();

        if (!$apiKey) {
            Log::warning('Exchange rate API key not configured');
            return false;
        }

        try {
            // Using exchangerate-api.com as example
            $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$baseCurrency->code}");

            if (!$response->successful()) {
                Log::error('Failed to fetch exchange rates', ['status' => $response->status()]);
                return false;
            }

            $data = $response->json();

            if ($data['result'] !== 'success') {
                Log::error('Exchange rate API error', ['error' => $data['error-type'] ?? 'Unknown']);
                return false;
            }

            $rates = $data['conversion_rates'] ?? [];

            foreach (Currency::all() as $currency) {
                if (isset($rates[$currency->code])) {
                    $currency->update([
                        'exchange_rate' => $rates[$currency->code],
                        'rate_updated_at' => now(),
                    ]);
                }
            }

            Currency::clearCache();

            Log::info('Exchange rates updated successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Exchange rate update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get all active currencies for dropdown.
     */
    public function getActiveCurrencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Currency::getActive();
    }

    /**
     * Get currencies for display in selector.
     */
    public function getCurrencyOptions(): array
    {
        return Currency::getActive()
            ->mapWithKeys(fn ($currency) => [
                $currency->code => "{$currency->code} - {$currency->name} ({$currency->symbol})"
            ])
            ->toArray();
    }
}
