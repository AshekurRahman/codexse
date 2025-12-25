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

        return view('seller.payouts.index', compact('seller', 'payouts'));
    }

    public function request(Request $request)
    {
        $seller = auth()->user()->seller;

        if ($seller->available_balance < 50) {
            return redirect()->back()->with('error', 'Minimum payout amount is $50.');
        }

        if (!$seller->stripe_account_id) {
            return redirect()->back()->with('error', 'Please connect your Stripe account first.');
        }

        $payout = Payout::create([
            'seller_id' => $seller->id,
            'amount' => $seller->available_balance,
            'status' => 'pending',
        ]);

        // Reset seller balance (in production, this would be done after actual payout)
        $seller->update(['available_balance' => 0]);

        return redirect()->back()->with('success', 'Payout request submitted successfully.');
    }
}
