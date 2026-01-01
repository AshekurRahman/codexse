<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();

        return view('pages.referrals.index', [
            'user' => $user,
            'wallet' => $wallet,
            'referralLink' => $user->referral_link,
            'referralCode' => $user->referral_code,
            'balance' => $user->referral_balance,
            'totalReferrals' => $user->total_referrals,
            'successfulReferrals' => $user->successful_referrals,
            'totalEarnings' => $user->total_referral_earnings,
            'referredUsers' => $user->referredUsers()->latest()->take(10)->get(),
            'recentRewards' => $user->referralRewards()->latest()->take(10)->get(),
            'signupReward' => ReferralSetting::getSignupRewardForReferrer(),
            'purchaseCommission' => ReferralSetting::getPurchaseCommissionPercent(),
            'minWithdrawal' => ReferralSetting::getMinWithdrawalAmount(),
        ]);
    }

    public function transferToWallet(Request $request)
    {
        $user = auth()->user();
        $minWithdrawal = ReferralSetting::getMinWithdrawalAmount();

        if ($user->referral_balance < $minWithdrawal) {
            return redirect()->back()->with('error', 'Minimum transfer amount is $' . number_format($minWithdrawal, 2));
        }

        $amount = $user->referral_balance;
        $wallet = $user->getOrCreateWallet();

        // Transfer referral balance to wallet
        $wallet->deposit(
            amount: $amount,
            description: 'Referral earnings transfer',
            paymentMethod: 'referral'
        );

        // Reset referral balance
        $user->update(['referral_balance' => 0]);

        return redirect()->back()->with('success', 'Successfully transferred $' . number_format($amount, 2) . ' to your wallet!');
    }
}
