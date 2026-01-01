<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $category,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null,
        ?string $riskLevel = null,
        bool $isSuspicious = false
    ): ActivityLog {
        $user = $user ?? Auth::user();
        $request = request();
        $userAgent = $request->userAgent();
        $deviceInfo = UserSession::parseUserAgent($userAgent);

        return ActivityLog::create([
            'user_id' => $user?->id,
            'causer_id' => Auth::id(),
            'causer_type' => self::getCauserType(),
            'action' => $action,
            'category' => $category,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => !empty($properties) ? $properties : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceInfo['device'],
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'risk_level' => $riskLevel ?? ActivityLog::RISK_LOW,
            'is_suspicious' => $isSuspicious,
        ]);
    }

    /**
     * Get the causer type based on current context
     */
    protected static function getCauserType(): string
    {
        if (!Auth::check()) {
            return ActivityLog::CAUSER_SYSTEM;
        }

        $user = Auth::user();

        // Check if user is accessing admin panel
        if (request()->is('admin/*') || request()->is('admin')) {
            return ActivityLog::CAUSER_ADMIN;
        }

        return ActivityLog::CAUSER_USER;
    }

    /**
     * Log a successful login
     */
    public static function logLogin(User $user): void
    {
        // Create user session
        UserSession::createSession($user);

        // Record login attempt
        LoginAttempt::recordAttempt($user->email, true, $user->id);

        // Log activity
        self::log(
            ActivityLog::ACTION_LOGIN,
            ActivityLog::CATEGORY_AUTH,
            'Logged in successfully',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log a failed login attempt
     */
    public static function logLoginFailed(string $email, string $reason, ?User $user = null): void
    {
        // Record login attempt
        LoginAttempt::recordAttempt($email, false, $user?->id, $reason);

        // Check for suspicious activity (multiple failures)
        $recentFailures = LoginAttempt::getRecentFailedAttempts($email, 30);
        $ipFailures = LoginAttempt::getRecentFailedAttemptsForIp(request()->ip(), 30);

        $isSuspicious = $recentFailures >= 5 || $ipFailures >= 10;
        $riskLevel = match (true) {
            $recentFailures >= 10 || $ipFailures >= 20 => ActivityLog::RISK_HIGH,
            $recentFailures >= 5 || $ipFailures >= 10 => ActivityLog::RISK_MEDIUM,
            default => ActivityLog::RISK_LOW,
        };

        // Log activity
        self::log(
            ActivityLog::ACTION_LOGIN_FAILED,
            ActivityLog::CATEGORY_AUTH,
            "Login attempt failed: {$reason}",
            $user,
            ['email' => $email, 'reason' => $reason],
            null,
            null,
            $user,
            $riskLevel,
            $isSuspicious
        );
    }

    /**
     * Log a logout
     */
    public static function logLogout(User $user): void
    {
        // Mark current session as logged out
        $session = UserSession::where('user_id', $user->id)
            ->where('session_id', session()->getId())
            ->first();

        $session?->logout();

        // Log activity
        self::log(
            ActivityLog::ACTION_LOGOUT,
            ActivityLog::CATEGORY_AUTH,
            'Logged out',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log a password change
     */
    public static function logPasswordChanged(User $user): void
    {
        self::log(
            ActivityLog::ACTION_PASSWORD_CHANGED,
            ActivityLog::CATEGORY_SECURITY,
            'Password was changed',
            $user,
            [],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log a password reset
     */
    public static function logPasswordReset(User $user): void
    {
        self::log(
            ActivityLog::ACTION_PASSWORD_RESET,
            ActivityLog::CATEGORY_SECURITY,
            'Password was reset',
            $user,
            [],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log email change
     */
    public static function logEmailChanged(User $user, string $oldEmail, string $newEmail): void
    {
        self::log(
            ActivityLog::ACTION_EMAIL_CHANGED,
            ActivityLog::CATEGORY_SECURITY,
            'Email address was changed',
            $user,
            [],
            ['email' => $oldEmail],
            ['email' => $newEmail],
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log profile update
     */
    public static function logProfileUpdated(User $user, array $oldValues = [], array $newValues = []): void
    {
        self::log(
            ActivityLog::ACTION_PROFILE_UPDATED,
            ActivityLog::CATEGORY_PROFILE,
            'Profile information was updated',
            $user,
            [],
            $oldValues,
            $newValues,
            $user
        );
    }

    /**
     * Log 2FA enabled
     */
    public static function log2faEnabled(User $user): void
    {
        self::log(
            ActivityLog::ACTION_2FA_ENABLED,
            ActivityLog::CATEGORY_SECURITY,
            'Two-factor authentication was enabled',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log 2FA disabled
     */
    public static function log2faDisabled(User $user): void
    {
        self::log(
            ActivityLog::ACTION_2FA_DISABLED,
            ActivityLog::CATEGORY_SECURITY,
            'Two-factor authentication was disabled',
            $user,
            [],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log order placed
     */
    public static function logOrderPlaced(User $user, Model $order): void
    {
        self::log(
            ActivityLog::ACTION_ORDER_PLACED,
            ActivityLog::CATEGORY_ORDER,
            'Order #' . $order->order_number . ' was placed',
            $order,
            ['order_number' => $order->order_number, 'total' => $order->total],
            null,
            null,
            $user
        );
    }

    /**
     * Log payment made
     */
    public static function logPaymentMade(User $user, Model $order, array $paymentDetails = []): void
    {
        self::log(
            ActivityLog::ACTION_PAYMENT_MADE,
            ActivityLog::CATEGORY_PAYMENT,
            'Payment made for order #' . $order->order_number,
            $order,
            $paymentDetails,
            null,
            null,
            $user
        );
    }

    /**
     * Log refund requested
     */
    public static function logRefundRequested(User $user, Model $order, string $reason = ''): void
    {
        self::log(
            ActivityLog::ACTION_REFUND_REQUESTED,
            ActivityLog::CATEGORY_PAYMENT,
            'Refund requested for order #' . $order->order_number,
            $order,
            ['reason' => $reason],
            null,
            null,
            $user
        );
    }

    /**
     * Log file download
     */
    public static function logDownload(User $user, Model $product, ?string $fileName = null): void
    {
        self::log(
            ActivityLog::ACTION_DOWNLOAD,
            ActivityLog::CATEGORY_ORDER,
            'Downloaded: ' . ($fileName ?? $product->name),
            $product,
            ['file_name' => $fileName],
            null,
            null,
            $user
        );
    }

    /**
     * Log review created
     */
    public static function logReviewCreated(User $user, Model $review): void
    {
        self::log(
            ActivityLog::ACTION_REVIEW_CREATED,
            ActivityLog::CATEGORY_PRODUCT,
            'Review was submitted',
            $review,
            ['rating' => $review->rating ?? null],
            null,
            null,
            $user
        );
    }

    /**
     * Log account created
     */
    public static function logAccountCreated(User $user): void
    {
        self::log(
            ActivityLog::ACTION_ACCOUNT_CREATED,
            ActivityLog::CATEGORY_AUTH,
            'Account was created',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log seller application
     */
    public static function logSellerApplied(User $user): void
    {
        self::log(
            ActivityLog::ACTION_SELLER_APPLIED,
            ActivityLog::CATEGORY_SELLER,
            'Applied to become a seller',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log seller approved
     */
    public static function logSellerApproved(User $user): void
    {
        self::log(
            ActivityLog::ACTION_SELLER_APPROVED,
            ActivityLog::CATEGORY_SELLER,
            'Seller application was approved',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log product created
     */
    public static function logProductCreated(User $user, Model $product): void
    {
        self::log(
            ActivityLog::ACTION_PRODUCT_CREATED,
            ActivityLog::CATEGORY_PRODUCT,
            'Product "' . $product->name . '" was created',
            $product,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log product updated
     */
    public static function logProductUpdated(User $user, Model $product, array $oldValues = [], array $newValues = []): void
    {
        self::log(
            ActivityLog::ACTION_PRODUCT_UPDATED,
            ActivityLog::CATEGORY_PRODUCT,
            'Product "' . $product->name . '" was updated',
            $product,
            [],
            $oldValues,
            $newValues,
            $user
        );
    }

    /**
     * Log consent updated
     */
    public static function logConsentUpdated(User $user, array $oldConsent, array $newConsent): void
    {
        self::log(
            ActivityLog::ACTION_CONSENT_UPDATED,
            ActivityLog::CATEGORY_PRIVACY,
            'Privacy consent settings were updated',
            $user,
            [],
            $oldConsent,
            $newConsent,
            $user
        );
    }

    /**
     * Log data export
     */
    public static function logDataExported(User $user): void
    {
        self::log(
            ActivityLog::ACTION_DATA_EXPORTED,
            ActivityLog::CATEGORY_PRIVACY,
            'Personal data was exported',
            $user,
            [],
            null,
            null,
            $user
        );
    }

    /**
     * Log session revoked
     */
    public static function logSessionRevoked(User $user, UserSession $session): void
    {
        self::log(
            ActivityLog::ACTION_SESSION_REVOKED,
            ActivityLog::CATEGORY_SECURITY,
            'Session was revoked: ' . $session->device_info,
            $session,
            ['ip_address' => $session->ip_address, 'device' => $session->device_info],
            null,
            null,
            $user
        );
    }

    /**
     * Get activity summary for a user
     */
    public static function getUserActivitySummary(User $user, int $days = 30): array
    {
        $logs = ActivityLog::forUser($user->id)->recent($days)->get();

        return [
            'total_activities' => $logs->count(),
            'by_category' => $logs->groupBy('category')->map->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'suspicious_count' => $logs->where('is_suspicious', true)->count(),
            'login_count' => $logs->where('action', ActivityLog::ACTION_LOGIN)->count(),
            'last_login' => $logs->where('action', ActivityLog::ACTION_LOGIN)->first()?->created_at,
        ];
    }

    /**
     * Get security alerts for a user
     */
    public static function getSecurityAlerts(User $user): array
    {
        $alerts = [];

        // Check for recent failed login attempts
        $recentFailures = LoginAttempt::forEmail($user->email)
            ->failed()
            ->recent(60)
            ->count();

        if ($recentFailures >= 3) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "There have been {$recentFailures} failed login attempts in the last hour.",
            ];
        }

        // Check for suspicious activity
        $suspiciousLogs = ActivityLog::forUser($user->id)
            ->suspicious()
            ->recent(7)
            ->count();

        if ($suspiciousLogs > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$suspiciousLogs} suspicious activities detected in the last 7 days.",
            ];
        }

        // Check for new device logins
        $newDevices = UserSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->active()
            ->count();

        if ($newDevices > 1) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$newDevices} new devices have logged in during the last 7 days.",
            ];
        }

        return $alerts;
    }
}
