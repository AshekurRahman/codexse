<?php

namespace App\Exceptions\Wallet;

class HoldExpiredException extends WalletException
{
    public function __construct(int $holdId, array $context = [])
    {
        parent::__construct(
            'Wallet hold has expired',
            'HOLD_EXPIRED',
            array_merge($context, ['hold_id' => $holdId])
        );
    }
}
