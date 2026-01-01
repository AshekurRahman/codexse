<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        $affiliate = auth()->user()->affiliate;

        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }

        $referrals = $affiliate->referrals()
            ->with('referredUser')
            ->latest()
            ->paginate(20);

        return view('pages.affiliate.dashboard', compact('affiliate', 'referrals'));
    }

    public function apply()
    {
        if (auth()->user()->affiliate) {
            return redirect()->route('affiliate.dashboard');
        }

        return view('pages.affiliate.apply');
    }

    public function store(Request $request)
    {
        if (auth()->user()->affiliate) {
            return redirect()->route('affiliate.dashboard');
        }

        $validated = $request->validate([
            'paypal_email' => 'required|email|max:255',
        ]);

        Affiliate::create([
            'user_id' => auth()->id(),
            'paypal_email' => $validated['paypal_email'],
            'commission_rate' => 10.00,
            'status' => 'pending',
        ]);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Your affiliate application has been submitted. We will review it shortly.');
    }

    public function settings()
    {
        $affiliate = auth()->user()->affiliate;

        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }

        return view('pages.affiliate.settings', compact('affiliate'));
    }

    public function updateSettings(Request $request)
    {
        $affiliate = auth()->user()->affiliate;

        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }

        $validated = $request->validate([
            'paypal_email' => 'required|email|max:255',
        ]);

        $affiliate->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function transferToWallet(Request $request)
    {
        $affiliate = auth()->user()->affiliate;

        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }

        $minWithdrawal = 25.00; // Minimum withdrawal for affiliates

        if ($affiliate->pending_earnings < $minWithdrawal) {
            return redirect()->back()->with('error', 'Minimum transfer amount is $' . number_format($minWithdrawal, 2));
        }

        $amount = $affiliate->pending_earnings;
        $wallet = auth()->user()->getOrCreateWallet();

        // Transfer to wallet
        $wallet->deposit(
            amount: $amount,
            description: 'Affiliate earnings transfer',
            paymentMethod: 'affiliate'
        );

        // Mark as paid
        $affiliate->markAsPaid($amount);

        return redirect()->back()->with('success', 'Successfully transferred $' . number_format($amount, 2) . ' to your wallet!');
    }
}
