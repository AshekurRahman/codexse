<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Central configuration for all security-related settings including
    | authentication, rate limiting, IP blocking, and admin access.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    */

    'auth' => [
        // Maximum failed login attempts before account lockout
        'max_attempts' => env('AUTH_MAX_ATTEMPTS', 5),

        // Account lockout duration in minutes
        'lockout_minutes' => env('AUTH_LOCKOUT_MINUTES', 15),

        // Require 2FA for admin users
        'admin_2fa_required' => env('AUTH_ADMIN_2FA_REQUIRED', true),

        // Password reset token expiry in minutes
        'password_reset_expiry' => env('AUTH_PASSWORD_RESET_EXPIRY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        // Global rate limit (requests per minute)
        'global' => env('RATE_LIMIT_GLOBAL', 60),

        // API rate limit (requests per minute)
        'api' => env('RATE_LIMIT_API', 60),

        // Login rate limit (attempts per minute)
        'login' => env('RATE_LIMIT_LOGIN', 5),

        // Whitelisted IPs (comma-separated) - bypass rate limiting
        'whitelist' => env('RATE_LIMIT_WHITELIST', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Blocking
    |--------------------------------------------------------------------------
    */

    'ip_blocking' => [
        // Auto-block IP after X suspicious requests
        'threshold' => env('IP_BLOCK_THRESHOLD', 10),

        // Block duration in hours
        'duration_hours' => env('IP_BLOCK_DURATION_HOURS', 24),

        // Enable auto-blocking
        'auto_block_enabled' => env('IP_AUTO_BLOCK_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Security
    |--------------------------------------------------------------------------
    */

    'admin' => [
        // IP whitelist for admin panel (comma-separated)
        // Leave empty to allow all IPs
        // Supports exact IPs and CIDR notation
        'allowed_ips' => env('ADMIN_ALLOWED_IPS', ''),

        // Require IP whitelist (if empty, allow all)
        'require_ip_whitelist' => env('ADMIN_REQUIRE_IP_WHITELIST', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Notifications
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        // Email to receive security alerts
        'email' => env('SECURITY_EMAIL'),

        // Notify on suspicious login attempts
        'notify_suspicious_login' => env('SECURITY_NOTIFY_SUSPICIOUS_LOGIN', true),

        // Notify on account lockout
        'notify_account_lockout' => env('SECURITY_NOTIFY_ACCOUNT_LOCKOUT', true),

        // Notify on brute force detection
        'notify_brute_force' => env('SECURITY_NOTIFY_BRUTE_FORCE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Protection
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        // Webhook replay protection TTL in seconds
        'replay_ttl' => env('WEBHOOK_REPLAY_TTL', 300),

        // Processed webhook cleanup after X days
        'cleanup_days' => env('WEBHOOK_CLEANUP_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        // Maximum file upload size in KB
        'max_size_kb' => env('UPLOAD_MAX_SIZE_KB', 51200),

        // Allowed file extensions (comma-separated)
        'allowed_extensions' => env('UPLOAD_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,webp,pdf,zip,rar'),

        // Maximum avatar size in KB
        'avatar_max_size_kb' => env('AVATAR_MAX_SIZE_KB', 2048),
    ],

];
