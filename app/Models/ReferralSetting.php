<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ReferralSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("referral_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("referral_setting_{$key}");
    }

    public static function getSignupRewardForReferrer(): float
    {
        return (float) static::get('signup_reward_referrer', 5.00);
    }

    public static function getSignupRewardForReferred(): float
    {
        return (float) static::get('signup_reward_referred', 5.00);
    }

    public static function getPurchaseCommissionPercent(): float
    {
        return (float) static::get('purchase_commission_percent', 10);
    }

    public static function getMinWithdrawalAmount(): float
    {
        return (float) static::get('min_withdrawal_amount', 20.00);
    }

    public static function isEnabled(): bool
    {
        return (bool) static::get('referral_program_enabled', true);
    }
}
