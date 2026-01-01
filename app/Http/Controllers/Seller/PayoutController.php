<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $seller = auth()->user()->seller;
        $payouts = $seller->payouts()->latest()->paginate(20);
        $wallet = auth()->user()->getOrCreateWallet();

        return view('seller.payouts.index', compact('seller', 'payouts', 'wallet'));
    }

    public function request(Request $request)
    {
        $seller = auth()->user()->seller;
        $wallet = auth()->user()->getOrCreateWallet();

        if ($wallet->balance < 50) {
            return redirect()->back()->with('error', 'Minimum payout amount is $50. Your wallet balance is ' . $wallet->formatted_balance);
        }

        if (!$seller->stripe_account_id) {
            return redirect()->back()->with('error', 'Please connect your Stripe account first.');
        }

        $payoutAmount = $wallet->balance;

        // Withdraw from wallet
        $wallet->withdraw(
            amount: $payoutAmount,
            description: 'Payout request to Stripe',
            paymentMethod: 'stripe_payout'
        );

        $payout = Payout::create([
            'seller_id' => $seller->id,
            'amount' => $payoutAmount,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Payout request of $' . number_format($payoutAmount, 2) . ' submitted successfully.');
    }
}
