<?php

namespace App\Exceptions\Wallet;

class InsufficientBalanceException extends WalletException
{
    public function __construct(float $required, float $available, array $context = [])
    {
        parent::__construct(
            "Insufficient balance. Required: {$required}, Available: {$available}",
            'INSUFFICIENT_BALANCE',
            array_merge($context, [
                'required' => $required,
                'available' => $available,
            ])
        );
    }
}
