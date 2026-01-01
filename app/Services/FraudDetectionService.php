<?php

namespace App\Services;

use App\Models\FraudAlert;
use App\Models\FraudRule;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\FraudAlertNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class FraudDetectionService
{
    protected array $triggeredRules = [];
    protected float $totalRiskScore = 0;
    protected array $detectionData = [];
    protected ?string $highestSeverity = null;
    protected bool $shouldBlock = false;

    /**
     * Analyze a transaction for potential fraud
     */
    public function analyze(
        ?User $user,
        float $amount,
        string $paymentMethod,
        ?Request $request = null,
        ?Model $transaction = null
    ): FraudAnalysisResult {
        $this->reset();

        $request = $request ?? request();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Run all enabled fraud detection rules
        $this->checkBlockedIp($ipAddress);
        $this->checkVelocity($user, $amount);
        $this->checkHighAmount($user, $amount);
        $this->checkNewAccount($user, $amount);
        $this->checkFailedAttempts($user, $ipAddress);
        $this->checkGeoAnomaly($user, $ipAddress);
        $this->checkMultiplePaymentMethods($user, $paymentMethod);
        $this->checkCardTesting($user, $amount, $ipAddress);

        // Calculate final risk score (cap at 100)
        $this->totalRiskScore = min(100, $this->totalRiskScore);

        // Determine if we should create an alert
        $shouldAlert = $this->totalRiskScore >= 25 || count($this->triggeredRules) >= 2;

        $alert = null;
        if ($shouldAlert) {
            $alert = $this->createAlert($user, $amount, $paymentMethod, $transaction, $request);
        }

        return new FraudAnalysisResult(
            passed: !$this->shouldBlock && $this->totalRiskScore < 80,
            riskScore: $this->totalRiskScore,
            triggeredRules: $this->triggeredRules,
            shouldBlock: $this->shouldBlock,
            alert: $alert,
            message: $this->getResultMessage()
        );
    }

    /**
     * Reset analysis state
     */
    protected function reset(): void
    {
        $this->triggeredRules = [];
        $this->totalRiskScore = 0;
        $this->detectionData = [];
        $this->highestSeverity = null;
        $this->shouldBlock = false;
    }

    /**
     * Check if IP is blocked
     */
    protected function checkBlockedIp(string $ipAddress): void
    {
        $blocked = DB::table('fraud_ip_blocklist')
            ->where('ip_address', $ipAddress)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($blocked) {
            $this->addTriggeredRule('blocked_ip', 100, 'critical', [
                'ip_address' => $ipAddress,
            ]);
            $this->shouldBlock = true;
        }
    }

    /**
     * Check transaction velocity
     */
    protected function checkVelocity(?User $user, float $amount): void
    {
        if (!$user) return;

        $rule = FraudRule::getRule('velocity_check');
        if (!$rule) return;

        $config = $rule->config;
        $hourlyCount = $this->getRecentTransactionCount($user->id, 1);
        $dailyCount = $this->getRecentTransactionCount($user->id, 24);
        $hourlyAmount = $this->getRecentTransactionAmount($user->id, 1);
        $dailyAmount = $this->getRecentTransactionAmount($user->id, 24);

        $triggered = false;
        $data = [];

        if ($hourlyCount >= ($config['max_transactions_per_hour'] ?? 5)) {
            $triggered = true;
            $data['hourly_transactions'] = $hourlyCount;
        }

        if ($dailyCount >= ($config['max_transactions_per_day'] ?? 15)) {
            $triggered = true;
            $data['daily_transactions'] = $dailyCount;
        }

        if (($hourlyAmount + $amount) >= ($config['max_amount_per_hour'] ?? 1000)) {
            $triggered = true;
            $data['hourly_amount'] = $hourlyAmount + $amount;
        }

        if (($dailyAmount + $amount) >= ($config['max_amount_per_day'] ?? 5000)) {
            $triggered = true;
            $data['daily_amount'] = $dailyAmount + $amount;
        }

        if ($triggered) {
            $this->addTriggeredRule('velocity_check', $rule->risk_score, $rule->severity, $data);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }
    }

    /**
     * Check for unusually high transaction amount
     */
    protected function checkHighAmount(?User $user, float $amount): void
    {
        $rule = FraudRule::getRule('high_amount');
        if (!$rule) return;

        $config = $rule->config;
        $threshold = $config['threshold'] ?? 500;

        // Lower threshold for new users
        if ($user && $user->created_at->diffInHours(now()) < 24) {
            $threshold = $config['new_user_threshold'] ?? 200;
        }

        if ($amount >= $threshold) {
            $this->addTriggeredRule('high_amount', $rule->risk_score, $rule->severity, [
                'amount' => $amount,
                'threshold' => $threshold,
            ]);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }
    }

    /**
     * Check new account risk
     */
    protected function checkNewAccount(?User $user, float $amount): void
    {
        if (!$user) return;

        $rule = FraudRule::getRule('new_account');
        if (!$rule) return;

        $config = $rule->config;
        $accountAgeHours = $user->created_at->diffInHours(now());
        $amountThreshold = $config['amount_threshold'] ?? 100;
        $ageThreshold = $config['account_age_hours'] ?? 24;

        if ($accountAgeHours < $ageThreshold && $amount >= $amountThreshold) {
            $this->addTriggeredRule('new_account', $rule->risk_score, $rule->severity, [
                'account_age_hours' => $accountAgeHours,
                'amount' => $amount,
            ]);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }
    }

    /**
     * Check for multiple failed payment attempts
     */
    protected function checkFailedAttempts(?User $user, string $ipAddress): void
    {
        $rule = FraudRule::getRule('failed_attempts');
        if (!$rule) return;

        $config = $rule->config;
        $cacheKey = 'fraud:failed:' . ($user?->id ?? 'ip:' . $ipAddress);

        $hourlyFailed = Cache::get($cacheKey . ':hourly', 0);
        $dailyFailed = Cache::get($cacheKey . ':daily', 0);

        $triggered = false;
        $data = [];

        if ($hourlyFailed >= ($config['max_failed_per_hour'] ?? 3)) {
            $triggered = true;
            $data['hourly_failed'] = $hourlyFailed;
        }

        if ($dailyFailed >= ($config['max_failed_per_day'] ?? 5)) {
            $triggered = true;
            $data['daily_failed'] = $dailyFailed;
        }

        if ($triggered) {
            $this->addTriggeredRule('failed_attempts', $rule->risk_score, $rule->severity, $data);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }
    }

    /**
     * Check for geographic anomalies
     */
    protected function checkGeoAnomaly(?User $user, string $ipAddress): void
    {
        $rule = FraudRule::getRule('geo_anomaly');
        if (!$rule) return;

        $config = $rule->config;
        $countryCode = $this->getCountryFromIp($ipAddress);

        if (!$countryCode) return;

        $highRiskCountries = $config['high_risk_countries'] ?? ['NG', 'RU', 'UA'];

        if (in_array($countryCode, $highRiskCountries)) {
            $this->addTriggeredRule('geo_anomaly', $rule->risk_score, $rule->severity, [
                'country_code' => $countryCode,
                'high_risk' => true,
            ]);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }

        // Check for sudden country change
        if ($user && ($config['check_country_change'] ?? true)) {
            $lastCountry = $this->getLastTransactionCountry($user->id);
            if ($lastCountry && $lastCountry !== $countryCode) {
                $this->addTriggeredRule('geo_anomaly', $rule->risk_score / 2, 'medium', [
                    'country_code' => $countryCode,
                    'previous_country' => $lastCountry,
                    'country_change' => true,
                ]);
            }
        }
    }

    /**
     * Check for multiple payment methods
     */
    protected function checkMultiplePaymentMethods(?User $user, string $paymentMethod): void
    {
        if (!$user) return;

        $rule = FraudRule::getRule('multiple_cards');
        if (!$rule) return;

        $config = $rule->config;
        $windowHours = $config['window_hours'] ?? 24;
        $maxMethods = $config['max_methods_per_day'] ?? 3;

        $recentMethods = Order::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours($windowHours))
            ->distinct()
            ->pluck('payment_method')
            ->toArray();

        // Add current method
        $recentMethods[] = $paymentMethod;
        $uniqueMethods = count(array_unique($recentMethods));

        if ($uniqueMethods >= $maxMethods) {
            $this->addTriggeredRule('multiple_cards', $rule->risk_score, $rule->severity, [
                'unique_methods' => $uniqueMethods,
                'methods' => array_unique($recentMethods),
            ]);
            if ($rule->auto_block) {
                $this->shouldBlock = true;
            }
        }
    }

    /**
     * Check for card testing patterns
     */
    protected function checkCardTesting(?User $user, float $amount, string $ipAddress): void
    {
        $rule = FraudRule::getRule('card_testing');
        if (!$rule) return;

        $config = $rule->config;
        $smallAmountThreshold = $config['small_amount_threshold'] ?? 5;
        $minSmallTransactions = $config['min_small_transactions'] ?? 3;
        $windowMinutes = $config['window_minutes'] ?? 30;

        // Check for pattern: multiple small transactions followed by larger one
        $identifier = $user?->id ?? 'ip:' . $ipAddress;

        $recentSmallTransactions = Order::where(function ($query) use ($user, $ipAddress) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->where('total', '<=', $smallAmountThreshold)
            ->count();

        if ($recentSmallTransactions >= $minSmallTransactions && $amount > $smallAmountThreshold * 10) {
            $this->addTriggeredRule('card_testing', $rule->risk_score, $rule->severity, [
                'small_transactions' => $recentSmallTransactions,
                'current_amount' => $amount,
            ]);
            $this->shouldBlock = true; // Always block card testing
        }
    }

    /**
     * Add a triggered rule
     */
    protected function addTriggeredRule(string $code, float $score, string $severity, array $data = []): void
    {
        $this->triggeredRules[] = [
            'code' => $code,
            'score' => $score,
            'severity' => $severity,
        ];
        $this->totalRiskScore += $score;
        $this->detectionData[$code] = $data;

        // Track highest severity
        $severityOrder = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        if (!$this->highestSeverity || ($severityOrder[$severity] ?? 0) > ($severityOrder[$this->highestSeverity] ?? 0)) {
            $this->highestSeverity = $severity;
        }
    }

    /**
     * Create fraud alert
     */
    protected function createAlert(
        ?User $user,
        float $amount,
        string $paymentMethod,
        ?Model $transaction,
        Request $request
    ): FraudAlert {
        $primaryType = $this->triggeredRules[0]['code'] ?? 'suspicious_pattern';
        $countryCode = $this->getCountryFromIp($request->ip());

        $alert = FraudAlert::create([
            'user_id' => $user?->id,
            'alertable_type' => $transaction ? get_class($transaction) : null,
            'alertable_id' => $transaction?->id,
            'type' => $primaryType,
            'severity' => $this->highestSeverity ?? 'medium',
            'status' => $this->shouldBlock ? 'pending' : 'pending',
            'risk_score' => $this->totalRiskScore,
            'transaction_amount' => $amount,
            'payment_method' => $paymentMethod,
            'detection_rules' => $this->triggeredRules,
            'detection_data' => $this->detectionData,
            'description' => $this->generateDescription(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'country_code' => $countryCode,
            'auto_blocked' => $this->shouldBlock,
        ]);

        // Send notification to admins for high-risk alerts
        if ($this->totalRiskScore >= 60 || $this->highestSeverity === 'critical') {
            $this->notifyAdmins($alert);
        }

        Log::channel('fraud')->warning('Fraud alert created', [
            'alert_id' => $alert->id,
            'user_id' => $user?->id,
            'risk_score' => $this->totalRiskScore,
            'rules' => array_column($this->triggeredRules, 'code'),
        ]);

        return $alert;
    }

    /**
     * Generate alert description
     */
    protected function generateDescription(): string
    {
        $ruleNames = array_map(function ($rule) {
            return FraudAlert::TYPES[$rule['code']] ?? $rule['code'];
        }, $this->triggeredRules);

        return 'Triggered rules: ' . implode(', ', $ruleNames);
    }

    /**
     * Get result message
     */
    protected function getResultMessage(): string
    {
        if ($this->shouldBlock) {
            return 'Transaction blocked due to suspicious activity.';
        }

        if ($this->totalRiskScore >= 80) {
            return 'High risk transaction detected.';
        }

        if ($this->totalRiskScore >= 50) {
            return 'Moderate risk detected.';
        }

        return 'Transaction passed fraud checks.';
    }

    /**
     * Notify admin users of fraud alert
     */
    protected function notifyAdmins(FraudAlert $alert): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            Notification::send($admins, new FraudAlertNotification($alert));
            $alert->update(['notification_sent' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to send fraud notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Record a failed payment attempt
     */
    public function recordFailedAttempt(?User $user, string $ipAddress): void
    {
        $cacheKey = 'fraud:failed:' . ($user?->id ?? 'ip:' . $ipAddress);

        Cache::increment($cacheKey . ':hourly');
        Cache::put($cacheKey . ':hourly', Cache::get($cacheKey . ':hourly', 1), now()->addHour());

        Cache::increment($cacheKey . ':daily');
        Cache::put($cacheKey . ':daily', Cache::get($cacheKey . ':daily', 1), now()->addDay());
    }

    /**
     * Clear failed attempts after successful payment
     */
    public function clearFailedAttempts(?User $user, string $ipAddress): void
    {
        $cacheKey = 'fraud:failed:' . ($user?->id ?? 'ip:' . $ipAddress);
        Cache::forget($cacheKey . ':hourly');
        Cache::forget($cacheKey . ':daily');
    }

    /**
     * Block an IP address
     */
    public function blockIp(string $ipAddress, string $reason, ?int $hours = null): void
    {
        DB::table('fraud_ip_blocklist')->updateOrInsert(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'type' => 'auto',
                'expires_at' => $hours ? now()->addHours($hours) : null,
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Get recent transaction count for user
     */
    protected function getRecentTransactionCount(int $userId, int $hours): int
    {
        return Order::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();
    }

    /**
     * Get recent transaction amount for user
     */
    protected function getRecentTransactionAmount(int $userId, int $hours): float
    {
        return Order::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours($hours))
            ->sum('total');
    }

    /**
     * Get country from IP address (simplified - in production use a GeoIP service)
     */
    protected function getCountryFromIp(string $ipAddress): ?string
    {
        // In production, use a GeoIP service like MaxMind
        // For now, return null or implement basic logic
        return Cache::remember("geoip:{$ipAddress}", 3600, function () use ($ipAddress) {
            // Placeholder - integrate with GeoIP service
            return null;
        });
    }

    /**
     * Get last transaction country for user
     */
    protected function getLastTransactionCountry(int $userId): ?string
    {
        $lastAlert = FraudAlert::where('user_id', $userId)
            ->whereNotNull('country_code')
            ->latest()
            ->first();

        return $lastAlert?->country_code;
    }
}

/**
 * Fraud Analysis Result DTO
 */
class FraudAnalysisResult
{
    public function __construct(
        public bool $passed,
        public float $riskScore,
        public array $triggeredRules,
        public bool $shouldBlock,
        public ?FraudAlert $alert,
        public string $message
    ) {}

    public function isHighRisk(): bool
    {
        return $this->riskScore >= 60;
    }

    public function toArray(): array
    {
        return [
            'passed' => $this->passed,
            'risk_score' => $this->riskScore,
            'triggered_rules' => $this->triggeredRules,
            'should_block' => $this->shouldBlock,
            'alert_id' => $this->alert?->id,
            'message' => $this->message,
        ];
    }
}
