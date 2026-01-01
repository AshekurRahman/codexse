<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_enabled',
        'severity',
        'risk_score',
        'config',
        'auto_block',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
        'auto_block' => 'boolean',
    ];

    // Default rule configurations
    public const DEFAULT_RULES = [
        'velocity_check' => [
            'name' => 'Transaction Velocity Check',
            'description' => 'Flags when user makes too many transactions in a short period',
            'severity' => 'high',
            'risk_score' => 40,
            'config' => [
                'max_transactions_per_hour' => 5,
                'max_transactions_per_day' => 15,
                'max_amount_per_hour' => 1000,
                'max_amount_per_day' => 5000,
            ],
        ],
        'high_amount' => [
            'name' => 'High Transaction Amount',
            'description' => 'Flags transactions above a certain threshold',
            'severity' => 'medium',
            'risk_score' => 30,
            'config' => [
                'threshold' => 500,
                'new_user_threshold' => 200,
            ],
        ],
        'new_account' => [
            'name' => 'New Account Risk',
            'description' => 'Flags high-value transactions from new accounts',
            'severity' => 'medium',
            'risk_score' => 25,
            'config' => [
                'account_age_hours' => 24,
                'amount_threshold' => 100,
            ],
        ],
        'failed_attempts' => [
            'name' => 'Multiple Failed Payment Attempts',
            'description' => 'Flags users with multiple failed payment attempts',
            'severity' => 'high',
            'risk_score' => 50,
            'config' => [
                'max_failed_per_hour' => 3,
                'max_failed_per_day' => 5,
            ],
        ],
        'geo_anomaly' => [
            'name' => 'Geographic Anomaly',
            'description' => 'Flags transactions from unusual locations or high-risk countries',
            'severity' => 'medium',
            'risk_score' => 35,
            'config' => [
                'check_country_change' => true,
                'high_risk_countries' => ['NG', 'RU', 'UA', 'BY', 'VN', 'ID', 'PH'],
            ],
        ],
        'multiple_cards' => [
            'name' => 'Multiple Payment Methods',
            'description' => 'Flags when user uses multiple cards/payment methods quickly',
            'severity' => 'high',
            'risk_score' => 45,
            'config' => [
                'max_methods_per_day' => 3,
                'window_hours' => 24,
            ],
        ],
        'card_testing' => [
            'name' => 'Card Testing Detection',
            'description' => 'Detects patterns consistent with card testing',
            'severity' => 'critical',
            'risk_score' => 80,
            'config' => [
                'small_amount_threshold' => 5,
                'min_small_transactions' => 3,
                'window_minutes' => 30,
            ],
        ],
        'blocked_ip' => [
            'name' => 'Blocked IP Check',
            'description' => 'Blocks transactions from known bad IPs',
            'severity' => 'critical',
            'risk_score' => 100,
            'auto_block' => true,
            'config' => [],
        ],
        'device_anomaly' => [
            'name' => 'Device Fingerprint Anomaly',
            'description' => 'Detects suspicious device patterns',
            'severity' => 'medium',
            'risk_score' => 30,
            'config' => [
                'max_users_per_device' => 2,
                'window_days' => 7,
            ],
        ],
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeAutoBlock($query)
    {
        return $query->where('auto_block', true);
    }

    public function getConfigValue(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public static function getRule(string $code): ?self
    {
        return static::where('code', $code)->where('is_enabled', true)->first();
    }

    public static function initializeDefaults(): void
    {
        foreach (self::DEFAULT_RULES as $code => $rule) {
            static::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $rule['name'],
                    'description' => $rule['description'],
                    'severity' => $rule['severity'],
                    'risk_score' => $rule['risk_score'],
                    'config' => $rule['config'] ?? [],
                    'auto_block' => $rule['auto_block'] ?? false,
                ]
            );
        }
    }
}
