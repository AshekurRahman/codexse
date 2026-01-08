<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'causer_id',
        'causer_type',
        'action',
        'category',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'country_code',
        'city',
        'risk_level',
        'is_suspicious',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'is_suspicious' => 'boolean',
    ];

    // Actions
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_LOGIN_FAILED = 'login_failed';
    public const ACTION_PASSWORD_CHANGED = 'password_changed';
    public const ACTION_PASSWORD_RESET = 'password_reset';
    public const ACTION_EMAIL_CHANGED = 'email_changed';
    public const ACTION_PROFILE_UPDATED = 'profile_updated';
    public const ACTION_2FA_ENABLED = '2fa_enabled';
    public const ACTION_2FA_DISABLED = '2fa_disabled';
    public const ACTION_ORDER_PLACED = 'order_placed';
    public const ACTION_ORDER_COMPLETED = 'order_completed';
    public const ACTION_PAYMENT_MADE = 'payment_made';
    public const ACTION_REFUND_REQUESTED = 'refund_requested';
    public const ACTION_DOWNLOAD = 'download';
    public const ACTION_REVIEW_CREATED = 'review_created';
    public const ACTION_ACCOUNT_CREATED = 'account_created';
    public const ACTION_ACCOUNT_DELETED = 'account_deleted';
    public const ACTION_SELLER_APPLIED = 'seller_applied';
    public const ACTION_SELLER_APPROVED = 'seller_approved';
    public const ACTION_PRODUCT_CREATED = 'product_created';
    public const ACTION_PRODUCT_UPDATED = 'product_updated';
    public const ACTION_CONSENT_UPDATED = 'consent_updated';
    public const ACTION_DATA_EXPORTED = 'data_exported';
    public const ACTION_SESSION_REVOKED = 'session_revoked';

    // Financial actions
    public const ACTION_WALLET_DEPOSIT = 'wallet_deposit';
    public const ACTION_WALLET_WITHDRAWAL = 'wallet_withdrawal';
    public const ACTION_PAYOUT_REQUESTED = 'payout_requested';
    public const ACTION_PAYOUT_APPROVED = 'payout_approved';
    public const ACTION_PAYOUT_REJECTED = 'payout_rejected';
    public const ACTION_PAYOUT_PROCESSED = 'payout_processed';
    public const ACTION_PAYOUT_FAILED = 'payout_failed';

    // Escrow actions
    public const ACTION_ESCROW_CREATED = 'escrow_created';
    public const ACTION_ESCROW_HELD = 'escrow_held';
    public const ACTION_ESCROW_RELEASED = 'escrow_released';
    public const ACTION_ESCROW_REFUNDED = 'escrow_refunded';
    public const ACTION_ESCROW_DISPUTED = 'escrow_disputed';

    // Admin actions
    public const ACTION_ADMIN_SELLER_APPROVED = 'admin_seller_approved';
    public const ACTION_ADMIN_SELLER_REJECTED = 'admin_seller_rejected';
    public const ACTION_ADMIN_PRODUCT_APPROVED = 'admin_product_approved';
    public const ACTION_ADMIN_PRODUCT_REJECTED = 'admin_product_rejected';
    public const ACTION_ADMIN_DISPUTE_RESOLVED = 'admin_dispute_resolved';
    public const ACTION_ADMIN_REFUND_ISSUED = 'admin_refund_issued';
    public const ACTION_ADMIN_USER_SUSPENDED = 'admin_user_suspended';
    public const ACTION_ADMIN_USER_UNSUSPENDED = 'admin_user_unsuspended';
    public const ACTION_ADMIN_ROLE_ASSIGNED = 'admin_role_assigned';
    public const ACTION_ADMIN_SETTING_CHANGED = 'admin_setting_changed';

    // Subscription actions
    public const ACTION_SUBSCRIPTION_CREATED = 'subscription_created';
    public const ACTION_SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
    public const ACTION_SUBSCRIPTION_RENEWED = 'subscription_renewed';

    // Order actions
    public const ACTION_ORDER_CANCELLED = 'order_cancelled';
    public const ACTION_ORDER_REFUNDED = 'order_refunded';

    // API actions
    public const ACTION_API_KEY_GENERATED = 'api_key_generated';
    public const ACTION_API_KEY_REVOKED = 'api_key_revoked';

    public const ACTIONS = [
        self::ACTION_LOGIN => 'Login',
        self::ACTION_LOGOUT => 'Logout',
        self::ACTION_LOGIN_FAILED => 'Login Failed',
        self::ACTION_PASSWORD_CHANGED => 'Password Changed',
        self::ACTION_PASSWORD_RESET => 'Password Reset',
        self::ACTION_EMAIL_CHANGED => 'Email Changed',
        self::ACTION_PROFILE_UPDATED => 'Profile Updated',
        self::ACTION_2FA_ENABLED => '2FA Enabled',
        self::ACTION_2FA_DISABLED => '2FA Disabled',
        self::ACTION_ORDER_PLACED => 'Order Placed',
        self::ACTION_ORDER_COMPLETED => 'Order Completed',
        self::ACTION_PAYMENT_MADE => 'Payment Made',
        self::ACTION_REFUND_REQUESTED => 'Refund Requested',
        self::ACTION_DOWNLOAD => 'Download',
        self::ACTION_REVIEW_CREATED => 'Review Created',
        self::ACTION_ACCOUNT_CREATED => 'Account Created',
        self::ACTION_ACCOUNT_DELETED => 'Account Deleted',
        self::ACTION_SELLER_APPLIED => 'Seller Application',
        self::ACTION_SELLER_APPROVED => 'Seller Approved',
        self::ACTION_PRODUCT_CREATED => 'Product Created',
        self::ACTION_PRODUCT_UPDATED => 'Product Updated',
        self::ACTION_CONSENT_UPDATED => 'Consent Updated',
        self::ACTION_DATA_EXPORTED => 'Data Exported',
        self::ACTION_SESSION_REVOKED => 'Session Revoked',
        // Financial actions
        self::ACTION_WALLET_DEPOSIT => 'Wallet Deposit',
        self::ACTION_WALLET_WITHDRAWAL => 'Wallet Withdrawal',
        self::ACTION_PAYOUT_REQUESTED => 'Payout Requested',
        self::ACTION_PAYOUT_APPROVED => 'Payout Approved',
        self::ACTION_PAYOUT_REJECTED => 'Payout Rejected',
        self::ACTION_PAYOUT_PROCESSED => 'Payout Processed',
        self::ACTION_PAYOUT_FAILED => 'Payout Failed',
        // Escrow actions
        self::ACTION_ESCROW_CREATED => 'Escrow Created',
        self::ACTION_ESCROW_HELD => 'Escrow Held',
        self::ACTION_ESCROW_RELEASED => 'Escrow Released',
        self::ACTION_ESCROW_REFUNDED => 'Escrow Refunded',
        self::ACTION_ESCROW_DISPUTED => 'Escrow Disputed',
        // Admin actions
        self::ACTION_ADMIN_SELLER_APPROVED => 'Seller Approved',
        self::ACTION_ADMIN_SELLER_REJECTED => 'Seller Rejected',
        self::ACTION_ADMIN_PRODUCT_APPROVED => 'Product Approved',
        self::ACTION_ADMIN_PRODUCT_REJECTED => 'Product Rejected',
        self::ACTION_ADMIN_DISPUTE_RESOLVED => 'Dispute Resolved',
        self::ACTION_ADMIN_REFUND_ISSUED => 'Refund Issued',
        self::ACTION_ADMIN_USER_SUSPENDED => 'User Suspended',
        self::ACTION_ADMIN_USER_UNSUSPENDED => 'User Unsuspended',
        self::ACTION_ADMIN_ROLE_ASSIGNED => 'Role Assigned',
        self::ACTION_ADMIN_SETTING_CHANGED => 'Setting Changed',
        // Subscription actions
        self::ACTION_SUBSCRIPTION_CREATED => 'Subscription Created',
        self::ACTION_SUBSCRIPTION_CANCELLED => 'Subscription Cancelled',
        self::ACTION_SUBSCRIPTION_RENEWED => 'Subscription Renewed',
        // Order actions
        self::ACTION_ORDER_CANCELLED => 'Order Cancelled',
        self::ACTION_ORDER_REFUNDED => 'Order Refunded',
        // API actions
        self::ACTION_API_KEY_GENERATED => 'API Key Generated',
        self::ACTION_API_KEY_REVOKED => 'API Key Revoked',
    ];

    // Categories
    public const CATEGORY_AUTH = 'auth';
    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_PROFILE = 'profile';
    public const CATEGORY_ORDER = 'order';
    public const CATEGORY_PAYMENT = 'payment';
    public const CATEGORY_PRODUCT = 'product';
    public const CATEGORY_SELLER = 'seller';
    public const CATEGORY_PRIVACY = 'privacy';
    public const CATEGORY_SYSTEM = 'system';
    public const CATEGORY_FINANCE = 'finance';
    public const CATEGORY_ADMIN = 'admin';
    public const CATEGORY_ESCROW = 'escrow';
    public const CATEGORY_SUBSCRIPTION = 'subscription';
    public const CATEGORY_API = 'api';

    public const CATEGORIES = [
        self::CATEGORY_AUTH => 'Authentication',
        self::CATEGORY_SECURITY => 'Security',
        self::CATEGORY_PROFILE => 'Profile',
        self::CATEGORY_ORDER => 'Orders',
        self::CATEGORY_PAYMENT => 'Payments',
        self::CATEGORY_PRODUCT => 'Products',
        self::CATEGORY_SELLER => 'Seller',
        self::CATEGORY_PRIVACY => 'Privacy',
        self::CATEGORY_SYSTEM => 'System',
        self::CATEGORY_FINANCE => 'Finance',
        self::CATEGORY_ADMIN => 'Admin Actions',
        self::CATEGORY_ESCROW => 'Escrow',
        self::CATEGORY_SUBSCRIPTION => 'Subscription',
        self::CATEGORY_API => 'API',
    ];

    // Causer types
    public const CAUSER_USER = 'user';
    public const CAUSER_ADMIN = 'admin';
    public const CAUSER_SYSTEM = 'system';

    // Risk levels
    public const RISK_LOW = 'low';
    public const RISK_MEDIUM = 'medium';
    public const RISK_HIGH = 'high';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeSecurityRelated($query)
    {
        return $query->whereIn('category', [self::CATEGORY_AUTH, self::CATEGORY_SECURITY]);
    }

    // Accessors
    public function getActionNameAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_LOGIN, self::ACTION_ACCOUNT_CREATED => 'success',
            self::ACTION_LOGOUT => 'info',
            self::ACTION_LOGIN_FAILED => 'danger',
            self::ACTION_PASSWORD_CHANGED, self::ACTION_PASSWORD_RESET => 'warning',
            self::ACTION_2FA_ENABLED => 'success',
            self::ACTION_2FA_DISABLED => 'warning',
            self::ACTION_ORDER_PLACED, self::ACTION_ORDER_COMPLETED => 'primary',
            self::ACTION_ACCOUNT_DELETED => 'danger',
            default => 'gray',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_LOGIN => 'heroicon-o-arrow-right-on-rectangle',
            self::ACTION_LOGOUT => 'heroicon-o-arrow-left-on-rectangle',
            self::ACTION_LOGIN_FAILED => 'heroicon-o-x-circle',
            self::ACTION_PASSWORD_CHANGED, self::ACTION_PASSWORD_RESET => 'heroicon-o-key',
            self::ACTION_EMAIL_CHANGED => 'heroicon-o-envelope',
            self::ACTION_PROFILE_UPDATED => 'heroicon-o-user',
            self::ACTION_2FA_ENABLED, self::ACTION_2FA_DISABLED => 'heroicon-o-shield-check',
            self::ACTION_ORDER_PLACED, self::ACTION_ORDER_COMPLETED => 'heroicon-o-shopping-cart',
            self::ACTION_PAYMENT_MADE => 'heroicon-o-credit-card',
            self::ACTION_DOWNLOAD => 'heroicon-o-arrow-down-tray',
            self::ACTION_ACCOUNT_CREATED => 'heroicon-o-user-plus',
            self::ACTION_ACCOUNT_DELETED => 'heroicon-o-trash',
            default => 'heroicon-o-document-text',
        };
    }

    public function getLocationAttribute(): ?string
    {
        if ($this->city && $this->country_code) {
            return "{$this->city}, {$this->country_code}";
        }
        return $this->country_code ?? $this->city;
    }

    public function getDeviceInfoAttribute(): string
    {
        $parts = array_filter([
            $this->device_type ? ucfirst($this->device_type) : null,
            $this->browser,
            $this->platform,
        ]);
        return implode(' / ', $parts) ?: 'Unknown Device';
    }
}
