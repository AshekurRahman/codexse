<?php

namespace App\Exceptions\Wallet;

class HoldNotFoundException extends WalletException
{
    public function __construct(int $holdId, array $context = [])
    {
        parent::__construct(
            'Wallet hold not found',
            'HOLD_NOT_FOUND',
            array_merge($context, ['hold_id' => $holdId])
        );
    }
}
