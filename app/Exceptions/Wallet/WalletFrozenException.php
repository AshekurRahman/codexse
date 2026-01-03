<?php

namespace App\Exceptions\Wallet;

class WalletFrozenException extends WalletException
{
    public function __construct(int $walletId, array $context = [])
    {
        parent::__construct(
            'Wallet is frozen and cannot process transactions',
            'WALLET_FROZEN',
            array_merge($context, ['wallet_id' => $walletId])
        );
    }
}
