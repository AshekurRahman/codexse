<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TaxRate;

class TaxService
{
    /**
     * Check if tax calculation is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) Setting::get('tax_enabled', false);
    }

    /**
     * Get the tax label for display.
     */
    public function getLabel(): string
    {
        return Setting::get('tax_label', 'Sales Tax');
    }

    /**
     * Check if tax should be displayed in cart.
     */
    public function showInCart(): bool
    {
        return (bool) Setting::get('tax_display_in_cart', true);
    }

    /**
     * Get tax rate for a US state.
     */
    public function getTaxRateForState(?string $stateCode): ?TaxRate
    {
        if (!$this->isEnabled() || empty($stateCode)) {
            return null;
        }

        return TaxRate::getForState($stateCode);
    }

    /**
     * Calculate tax for a given amount and state.
     *
     * @return array{tax_rate: float, tax_amount: float, taxable_amount: float}
     */
    public function calculateTax(float $amount, ?string $stateCode): array
    {
        $result = [
            'tax_rate' => 0,
            'tax_amount' => 0,
            'taxable_amount' => $amount,
            'state_code' => $stateCode,
            'tax_name' => null,
        ];

        if (!$this->isEnabled() || $amount <= 0) {
            return $result;
        }

        $taxRate = $this->getTaxRateForState($stateCode);

        if (!$taxRate) {
            return $result;
        }

        $result['tax_rate'] = (float) $taxRate->rate;
        $result['tax_amount'] = $taxRate->calculateTax($amount);
        $result['tax_name'] = $taxRate->name;

        return $result;
    }

    /**
     * Calculate total including tax.
     *
     * @return array{subtotal: float, discount: float, taxable_amount: float, tax_rate: float, tax_amount: float, total: float}
     */
    public function calculateTotals(float $subtotal, float $discount, ?string $stateCode): array
    {
        $taxableAmount = max(0, $subtotal - $discount);
        $taxData = $this->calculateTax($taxableAmount, $stateCode);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'taxable_amount' => $taxableAmount,
            'tax_rate' => $taxData['tax_rate'],
            'tax_amount' => $taxData['tax_amount'],
            'tax_name' => $taxData['tax_name'],
            'state_code' => $stateCode,
            'total' => $taxableAmount + $taxData['tax_amount'],
        ];
    }

    /**
     * Get list of US states.
     */
    public function getUsStates(): array
    {
        return config('tax.states', []);
    }

    /**
     * Get state name by code.
     */
    public function getStateName(string $stateCode): ?string
    {
        return config("tax.states.{$stateCode}");
    }

    /**
     * Format tax rate for display.
     */
    public function formatTaxRate(float $rate): string
    {
        return number_format($rate, 2) . '%';
    }
}
