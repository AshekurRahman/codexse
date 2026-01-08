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

    // ==========================================
    // FINANCIAL ACTIONS
    // ==========================================

    /**
     * Log wallet deposit
     */
    public static function logWalletDeposit(User $user, Model $wallet, float $amount, string $method, ?string $transactionId = null): void
    {
        self::log(
            ActivityLog::ACTION_WALLET_DEPOSIT,
            ActivityLog::CATEGORY_FINANCE,
            "Deposited $" . number_format($amount, 2) . " via {$method}",
            $wallet,
            [
                'amount' => $amount,
                'method' => $method,
                'transaction_id' => $transactionId,
                'wallet_balance_after' => $wallet->balance ?? null,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log wallet withdrawal
     */
    public static function logWalletWithdrawal(User $user, Model $wallet, float $amount, string $reason): void
    {
        self::log(
            ActivityLog::ACTION_WALLET_WITHDRAWAL,
            ActivityLog::CATEGORY_FINANCE,
            "Withdrew $" . number_format($amount, 2) . " - {$reason}",
            $wallet,
            [
                'amount' => $amount,
                'reason' => $reason,
                'wallet_balance_after' => $wallet->balance ?? null,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log payout request
     */
    public static function logPayoutRequested(User $user, Model $payout, float $amount, string $method): void
    {
        self::log(
            ActivityLog::ACTION_PAYOUT_REQUESTED,
            ActivityLog::CATEGORY_FINANCE,
            "Requested payout of $" . number_format($amount, 2) . " via {$method}",
            $payout,
            [
                'amount' => $amount,
                'method' => $method,
                'payout_id' => $payout->id,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log payout processed
     */
    public static function logPayoutProcessed(Model $payout, ?User $processedBy = null): void
    {
        $seller = $payout->seller;
        $user = $seller?->user;

        self::log(
            ActivityLog::ACTION_PAYOUT_PROCESSED,
            ActivityLog::CATEGORY_FINANCE,
            "Payout of $" . number_format($payout->amount, 2) . " processed",
            $payout,
            [
                'amount' => $payout->amount,
                'processed_by' => $processedBy?->id,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log payout approved by admin
     */
    public static function logPayoutApproved(User $admin, Model $payout, ?string $notes = null): void
    {
        $seller = $payout->seller;
        $sellerUser = $seller?->user;

        self::log(
            ActivityLog::ACTION_PAYOUT_APPROVED,
            ActivityLog::CATEGORY_ADMIN,
            "Approved payout of $" . number_format($payout->amount, 2) . " for " . ($seller?->business_name ?? 'unknown seller'),
            $payout,
            [
                'amount' => $payout->amount,
                'seller_id' => $seller?->id,
                'seller_name' => $seller?->business_name,
                'seller_email' => $sellerUser?->email,
                'notes' => $notes,
            ],
            null,
            null,
            $admin,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log payout rejected by admin
     */
    public static function logPayoutRejected(User $admin, Model $payout, string $reason): void
    {
        $seller = $payout->seller;
        $sellerUser = $seller?->user;

        self::log(
            ActivityLog::ACTION_PAYOUT_REJECTED,
            ActivityLog::CATEGORY_ADMIN,
            "Rejected payout of $" . number_format($payout->amount, 2) . " for " . ($seller?->business_name ?? 'unknown seller'),
            $payout,
            [
                'amount' => $payout->amount,
                'seller_id' => $seller?->id,
                'seller_name' => $seller?->business_name,
                'seller_email' => $sellerUser?->email,
                'rejection_reason' => $reason,
            ],
            null,
            null,
            $admin,
            ActivityLog::RISK_HIGH
        );
    }

    // ==========================================
    // ESCROW ACTIONS
    // ==========================================

    /**
     * Log escrow created
     */
    public static function logEscrowCreated(Model $transaction, User $payer): void
    {
        self::log(
            ActivityLog::ACTION_ESCROW_CREATED,
            ActivityLog::CATEGORY_ESCROW,
            "Escrow created for $" . number_format($transaction->amount, 2),
            $transaction,
            [
                'amount' => $transaction->amount,
                'platform_fee' => $transaction->platform_fee,
                'seller_amount' => $transaction->seller_amount,
                'seller_id' => $transaction->seller_id,
            ],
            null,
            null,
            $payer,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log escrow funds held
     */
    public static function logEscrowHeld(Model $transaction): void
    {
        $payer = User::find($transaction->payer_id);

        self::log(
            ActivityLog::ACTION_ESCROW_HELD,
            ActivityLog::CATEGORY_ESCROW,
            "Escrow funds held: $" . number_format($transaction->amount, 2),
            $transaction,
            [
                'amount' => $transaction->amount,
                'transaction_number' => $transaction->transaction_number ?? null,
            ],
            null,
            null,
            $payer,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log escrow funds released
     */
    public static function logEscrowReleased(Model $transaction, ?User $releasedBy = null, ?string $notes = null): void
    {
        $payee = User::find($transaction->payee_id);

        self::log(
            ActivityLog::ACTION_ESCROW_RELEASED,
            ActivityLog::CATEGORY_ESCROW,
            "Escrow released: $" . number_format($transaction->seller_amount, 2) . " to seller",
            $transaction,
            [
                'amount' => $transaction->amount,
                'seller_amount' => $transaction->seller_amount,
                'platform_fee' => $transaction->platform_fee,
                'released_by' => $releasedBy?->id,
                'notes' => $notes,
            ],
            null,
            null,
            $payee,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log escrow refunded
     */
    public static function logEscrowRefunded(Model $transaction, ?User $refundedBy = null, ?string $reason = null): void
    {
        $payer = User::find($transaction->payer_id);

        self::log(
            ActivityLog::ACTION_ESCROW_REFUNDED,
            ActivityLog::CATEGORY_ESCROW,
            "Escrow refunded: $" . number_format($transaction->amount, 2),
            $transaction,
            [
                'amount' => $transaction->amount,
                'refunded_by' => $refundedBy?->id,
                'reason' => $reason,
            ],
            null,
            null,
            $payer,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log escrow disputed
     */
    public static function logEscrowDisputed(Model $transaction, User $disputedBy): void
    {
        self::log(
            ActivityLog::ACTION_ESCROW_DISPUTED,
            ActivityLog::CATEGORY_ESCROW,
            "Escrow disputed for $" . number_format($transaction->amount, 2),
            $transaction,
            [
                'amount' => $transaction->amount,
                'disputed_by' => $disputedBy->id,
            ],
            null,
            null,
            $disputedBy,
            ActivityLog::RISK_HIGH,
            true // Mark as suspicious for review
        );
    }

    // ==========================================
    // ADMIN ACTIONS
    // ==========================================

    /**
     * Log admin seller approval
     */
    public static function logAdminSellerApproved(Model $seller, User $admin): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_SELLER_APPROVED,
            ActivityLog::CATEGORY_ADMIN,
            "Approved seller: {$seller->store_name}",
            $seller,
            [
                'seller_id' => $seller->id,
                'store_name' => $seller->store_name,
                'approved_by' => $admin->id,
            ],
            null,
            null,
            $seller->user,
            ActivityLog::RISK_LOW
        );
    }

    /**
     * Log admin seller rejection
     */
    public static function logAdminSellerRejected(Model $seller, User $admin, ?string $reason = null): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_SELLER_REJECTED,
            ActivityLog::CATEGORY_ADMIN,
            "Rejected seller: {$seller->store_name}",
            $seller,
            [
                'seller_id' => $seller->id,
                'store_name' => $seller->store_name,
                'rejected_by' => $admin->id,
                'reason' => $reason,
            ],
            null,
            null,
            $seller->user,
            ActivityLog::RISK_LOW
        );
    }

    /**
     * Log admin product approval
     */
    public static function logAdminProductApproved(Model $product, User $admin): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_PRODUCT_APPROVED,
            ActivityLog::CATEGORY_ADMIN,
            "Approved product: {$product->name}",
            $product,
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'approved_by' => $admin->id,
            ],
            null,
            null,
            $product->seller?->user
        );
    }

    /**
     * Log admin product rejection
     */
    public static function logAdminProductRejected(Model $product, User $admin, ?string $reason = null): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_PRODUCT_REJECTED,
            ActivityLog::CATEGORY_ADMIN,
            "Rejected product: {$product->name}",
            $product,
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'rejected_by' => $admin->id,
                'reason' => $reason,
            ],
            null,
            null,
            $product->seller?->user
        );
    }

    /**
     * Log admin dispute resolution
     */
    public static function logAdminDisputeResolved(Model $dispute, User $admin, string $resolution, ?float $refundAmount = null): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_DISPUTE_RESOLVED,
            ActivityLog::CATEGORY_ADMIN,
            "Resolved dispute #{$dispute->id}: {$resolution}",
            $dispute,
            [
                'dispute_id' => $dispute->id,
                'resolution' => $resolution,
                'refund_amount' => $refundAmount,
                'resolved_by' => $admin->id,
            ],
            null,
            null,
            User::find($dispute->initiated_by),
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log admin refund issued
     */
    public static function logAdminRefundIssued(Model $order, User $admin, float $amount, string $reason): void
    {
        $buyer = $order->buyer ?? $order->user;

        self::log(
            ActivityLog::ACTION_ADMIN_REFUND_ISSUED,
            ActivityLog::CATEGORY_ADMIN,
            "Refund of $" . number_format($amount, 2) . " issued for order #{$order->order_number}",
            $order,
            [
                'order_id' => $order->id,
                'amount' => $amount,
                'reason' => $reason,
                'issued_by' => $admin->id,
            ],
            null,
            null,
            $buyer,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log admin user suspension
     */
    public static function logAdminUserSuspended(User $user, User $admin, string $reason): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_USER_SUSPENDED,
            ActivityLog::CATEGORY_ADMIN,
            "User suspended: {$user->email}",
            $user,
            [
                'user_id' => $user->id,
                'reason' => $reason,
                'suspended_by' => $admin->id,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log admin user unsuspension
     */
    public static function logAdminUserUnsuspended(User $user, User $admin): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_USER_UNSUSPENDED,
            ActivityLog::CATEGORY_ADMIN,
            "User unsuspended: {$user->email}",
            $user,
            [
                'user_id' => $user->id,
                'unsuspended_by' => $admin->id,
            ],
            null,
            null,
            $user
        );
    }

    /**
     * Log admin role assignment
     */
    public static function logAdminRoleAssigned(User $user, User $admin, string $role, bool $added = true): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_ROLE_ASSIGNED,
            ActivityLog::CATEGORY_ADMIN,
            ($added ? "Added" : "Removed") . " role '{$role}' for user {$user->email}",
            $user,
            [
                'user_id' => $user->id,
                'role' => $role,
                'action' => $added ? 'added' : 'removed',
                'assigned_by' => $admin->id,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log admin setting change
     */
    public static function logAdminSettingChanged(User $admin, string $setting, $oldValue, $newValue): void
    {
        self::log(
            ActivityLog::ACTION_ADMIN_SETTING_CHANGED,
            ActivityLog::CATEGORY_ADMIN,
            "Changed setting: {$setting}",
            null,
            [
                'setting' => $setting,
                'changed_by' => $admin->id,
            ],
            ['value' => $oldValue],
            ['value' => $newValue],
            $admin,
            ActivityLog::RISK_MEDIUM
        );
    }

    // ==========================================
    // SUBSCRIPTION ACTIONS
    // ==========================================

    /**
     * Log subscription created
     */
    public static function logSubscriptionCreated(User $user, Model $subscription): void
    {
        self::log(
            ActivityLog::ACTION_SUBSCRIPTION_CREATED,
            ActivityLog::CATEGORY_SUBSCRIPTION,
            "Created subscription: " . ($subscription->plan_name ?? 'Plan'),
            $subscription,
            [
                'subscription_id' => $subscription->id,
                'plan' => $subscription->plan_name ?? null,
                'amount' => $subscription->amount ?? null,
            ],
            null,
            null,
            $user
        );
    }

    /**
     * Log subscription cancelled
     */
    public static function logSubscriptionCancelled(User $user, Model $subscription, ?string $reason = null): void
    {
        self::log(
            ActivityLog::ACTION_SUBSCRIPTION_CANCELLED,
            ActivityLog::CATEGORY_SUBSCRIPTION,
            "Cancelled subscription: " . ($subscription->plan_name ?? 'Plan'),
            $subscription,
            [
                'subscription_id' => $subscription->id,
                'reason' => $reason,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log subscription renewed
     */
    public static function logSubscriptionRenewed(User $user, Model $subscription): void
    {
        self::log(
            ActivityLog::ACTION_SUBSCRIPTION_RENEWED,
            ActivityLog::CATEGORY_SUBSCRIPTION,
            "Subscription renewed: " . ($subscription->plan_name ?? 'Plan'),
            $subscription,
            [
                'subscription_id' => $subscription->id,
            ],
            null,
            null,
            $user
        );
    }

    // ==========================================
    // ORDER ACTIONS
    // ==========================================

    /**
     * Log order cancellation
     */
    public static function logOrderCancelled(User $user, Model $order, string $reason): void
    {
        self::log(
            ActivityLog::ACTION_ORDER_CANCELLED,
            ActivityLog::CATEGORY_ORDER,
            "Order #{$order->order_number} cancelled",
            $order,
            [
                'order_number' => $order->order_number,
                'reason' => $reason,
                'total' => $order->total,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log order refund
     */
    public static function logOrderRefunded(User $user, Model $order, float $amount, string $reason): void
    {
        self::log(
            ActivityLog::ACTION_ORDER_REFUNDED,
            ActivityLog::CATEGORY_ORDER,
            "Order #{$order->order_number} refunded: $" . number_format($amount, 2),
            $order,
            [
                'order_number' => $order->order_number,
                'amount' => $amount,
                'reason' => $reason,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    // ==========================================
    // API ACTIONS
    // ==========================================

    /**
     * Log API key generated
     */
    public static function logApiKeyGenerated(User $user, ?string $keyName = null): void
    {
        self::log(
            ActivityLog::ACTION_API_KEY_GENERATED,
            ActivityLog::CATEGORY_API,
            "API key generated" . ($keyName ? ": {$keyName}" : ''),
            $user,
            [
                'key_name' => $keyName,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_HIGH
        );
    }

    /**
     * Log API key revoked
     */
    public static function logApiKeyRevoked(User $user, ?string $keyName = null): void
    {
        self::log(
            ActivityLog::ACTION_API_KEY_REVOKED,
            ActivityLog::CATEGORY_API,
            "API key revoked" . ($keyName ? ": {$keyName}" : ''),
            $user,
            [
                'key_name' => $keyName,
            ],
            null,
            null,
            $user,
            ActivityLog::RISK_MEDIUM
        );
    }

    /**
     * Log email changed
     */
    public static function logEmailChanged(User $user, string $oldEmail): void
    {
        self::log(
            ActivityLog::ACTION_EMAIL_CHANGED,
            ActivityLog::CATEGORY_SECURITY,
            "Email changed from {$oldEmail} to {$user->email}",
            $user,
            [
                'old_email' => $oldEmail,
                'new_email' => $user->email,
            ],
            ['email' => $oldEmail],
            ['email' => $user->email],
            $user,
            ActivityLog::RISK_HIGH
        );
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
