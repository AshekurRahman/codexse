<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    /**
     * Switch to a different currency.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $success = $this->currencyService->setCurrency($request->currency);

        if ($request->wantsJson()) {
            if ($success) {
                return response()->json([
                    'success' => true,
                    'currency' => $this->currencyService->getCurrentCurrency(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid currency',
            ], 400);
        }

        if (!$success) {
            return redirect()->back()->with('error', 'Invalid currency selected.');
        }

        return redirect()->back()->with('success', 'Currency updated successfully.');
    }

    /**
     * Get available currencies for selector.
     */
    public function list()
    {
        $currencies = Currency::getActive()->map(fn ($currency) => [
            'code' => $currency->code,
            'name' => $currency->name,
            'symbol' => $currency->symbol,
        ]);

        return response()->json([
            'currencies' => $currencies,
            'current' => $this->currencyService->getCurrentCurrencyCode(),
        ]);
    }

    /**
     * Convert a price to user's currency.
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'to' => 'nullable|string|size:3',
        ]);

        $amount = (float) $request->amount;
        $toCurrency = $request->to;

        $converted = $this->currencyService->convert($amount, $toCurrency);
        $formatted = $this->currencyService->format($amount, $toCurrency);

        return response()->json([
            'original' => $amount,
            'converted' => $converted,
            'formatted' => $formatted,
            'currency' => $toCurrency ?? $this->currencyService->getCurrentCurrencyCode(),
        ]);
    }
}
