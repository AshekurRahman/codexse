<?php

namespace App\Exceptions\Wallet;

class WalletInactiveException extends WalletException
{
    public function __construct(int $walletId, array $context = [])
    {
        parent::__construct(
            'Wallet is not active',
            'WALLET_INACTIVE',
            array_merge($context, ['wallet_id' => $walletId])
        );
    }
}
