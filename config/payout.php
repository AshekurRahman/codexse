<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payout Configuration
    |--------------------------------------------------------------------------
    |
    | Configure seller payout settings including minimum amounts,
    | approval requirements, and processing options.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Minimum Payout Amount
    |--------------------------------------------------------------------------
    |
    | Sellers must have at least this amount available to request a payout.
    |
    */

    'min_amount' => env('PAYOUT_MIN_AMOUNT', 50.00),

    /*
    |--------------------------------------------------------------------------
    | Maximum Payout Amount
    |--------------------------------------------------------------------------
    |
    | Maximum amount per payout request (0 for unlimited).
    |
    */

    'max_amount' => env('PAYOUT_MAX_AMOUNT', 0),

    /*
    |--------------------------------------------------------------------------
    | Approval Workflow
    |--------------------------------------------------------------------------
    */

    // Require admin approval for payouts
    'require_approval' => env('PAYOUT_REQUIRE_APPROVAL', true),

    // Auto-approve payouts under this amount (0 to always require approval)
    'auto_approve_under' => env('PAYOUT_AUTO_APPROVE_UNDER', 0),

    /*
    |--------------------------------------------------------------------------
    | Processing Settings
    |--------------------------------------------------------------------------
    */

    // Processing fee percentage (deducted from payout)
    'processing_fee_percent' => env('PAYOUT_PROCESSING_FEE_PERCENT', 0),

    // Fixed processing fee
    'processing_fee_fixed' => env('PAYOUT_PROCESSING_FEE_FIXED', 0),

    // Minimum days between payout requests
    'min_days_between_requests' => env('PAYOUT_MIN_DAYS_BETWEEN', 0),

    /*
    |--------------------------------------------------------------------------
    | Available Payout Methods
    |--------------------------------------------------------------------------
    */

    'methods' => [
        'paypal' => [
            'enabled' => env('PAYOUT_PAYPAL_ENABLED', true),
            'name' => 'PayPal',
        ],
        'payoneer' => [
            'enabled' => env('PAYOUT_PAYONEER_ENABLED', true),
            'name' => 'Payoneer',
        ],
        'bank_transfer' => [
            'enabled' => env('PAYOUT_BANK_ENABLED', true),
            'name' => 'Bank Transfer',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    // Notify seller when payout is approved
    'notify_on_approval' => env('PAYOUT_NOTIFY_ON_APPROVAL', true),

    // Notify seller when payout is rejected
    'notify_on_rejection' => env('PAYOUT_NOTIFY_ON_REJECTION', true),

    // Notify seller when payout is processed
    'notify_on_processing' => env('PAYOUT_NOTIFY_ON_PROCESSING', true),

];
