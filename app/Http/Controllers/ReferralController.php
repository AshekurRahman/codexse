<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('pages.referrals.index', [
            'user' => $user,
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
}
