<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fraud Detection Configuration
    |--------------------------------------------------------------------------
    |
    | Configure fraud detection thresholds and rules for transactions.
    | The system analyzes transactions for suspicious patterns.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable
    |--------------------------------------------------------------------------
    */

    'enabled' => env('FRAUD_DETECTION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Amount Thresholds
    |--------------------------------------------------------------------------
    */

    // Transactions above this amount trigger additional scrutiny
    'high_amount_threshold' => env('FRAUD_HIGH_AMOUNT_THRESHOLD', 500),

    // Very high risk threshold
    'very_high_amount_threshold' => env('FRAUD_VERY_HIGH_AMOUNT_THRESHOLD', 1000),

    /*
    |--------------------------------------------------------------------------
    | Velocity Rules
    |--------------------------------------------------------------------------
    |
    | Detect rapid/unusual transaction patterns.
    |
    */

    // Maximum transactions per hour per user
    'max_transactions_per_hour' => env('FRAUD_MAX_TRANSACTIONS_PER_HOUR', 10),

    // Maximum transactions per day per user
    'max_transactions_per_day' => env('FRAUD_MAX_TRANSACTIONS_PER_DAY', 50),

    // Maximum total amount per day per user
    'max_amount_per_day' => env('FRAUD_MAX_AMOUNT_PER_DAY', 2000),

    /*
    |--------------------------------------------------------------------------
    | New Account Risk
    |--------------------------------------------------------------------------
    |
    | New accounts are considered higher risk for fraud.
    |
    */

    // Account age in days to be considered "new"
    'new_account_days' => env('FRAUD_NEW_ACCOUNT_DAYS', 7),

    // Reduce limits for new accounts (percentage of normal limits)
    'new_account_limit_percent' => env('FRAUD_NEW_ACCOUNT_LIMIT_PERCENT', 50),

    /*
    |--------------------------------------------------------------------------
    | Geographic Rules
    |--------------------------------------------------------------------------
    */

    // Enable geographic anomaly detection
    'geo_detection_enabled' => env('FRAUD_GEO_DETECTION_ENABLED', true),

    // Flag transactions from different country than user profile
    'flag_country_mismatch' => env('FRAUD_FLAG_COUNTRY_MISMATCH', true),

    /*
    |--------------------------------------------------------------------------
    | Risk Scoring
    |--------------------------------------------------------------------------
    |
    | Risk scores determine transaction handling:
    | - Low (0-30): Process normally
    | - Medium (31-60): Log for review
    | - High (61-80): Require additional verification
    | - Critical (81-100): Block transaction
    |
    */

    'risk_thresholds' => [
        'low' => 30,
        'medium' => 60,
        'high' => 80,
        'critical' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    // Automatically block high-risk transactions
    'auto_block_high_risk' => env('FRAUD_AUTO_BLOCK_HIGH_RISK', false),

    // Require manual review for medium-risk transactions
    'require_review_medium_risk' => env('FRAUD_REQUIRE_REVIEW_MEDIUM_RISK', true),

    // Notify admins of detected fraud
    'notify_admins' => env('FRAUD_NOTIFY_ADMINS', true),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    // Log all fraud checks (not just flagged ones)
    'log_all_checks' => env('FRAUD_LOG_ALL_CHECKS', false),

    // Fraud log retention in days
    'log_retention_days' => env('FRAUD_LOG_RETENTION_DAYS', 90),

];
