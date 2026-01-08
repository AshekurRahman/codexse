<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Escrow System Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the escrow system for marketplace transactions.
    | Funds are held in escrow until the buyer confirms delivery.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Platform Fee
    |--------------------------------------------------------------------------
    |
    | The percentage fee taken by the platform from each transaction.
    | This is deducted when funds are released to the seller.
    |
    */

    'platform_fee_percent' => env('ESCROW_PLATFORM_FEE_PERCENT', 20),

    /*
    |--------------------------------------------------------------------------
    | Auto-Release Settings
    |--------------------------------------------------------------------------
    |
    | Funds are automatically released to the seller after this many days
    | if the buyer doesn't confirm delivery or open a dispute.
    |
    */

    'auto_release_days' => env('ESCROW_AUTO_RELEASE_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Dispute Window
    |--------------------------------------------------------------------------
    |
    | Number of days after delivery confirmation that a buyer can
    | still open a dispute.
    |
    */

    'dispute_window_days' => env('ESCROW_DISPUTE_WINDOW_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Minimum Escrow Amount
    |--------------------------------------------------------------------------
    |
    | Minimum transaction amount to use escrow protection.
    |
    */

    'min_amount' => env('ESCROW_MIN_AMOUNT', 1.00),

];
