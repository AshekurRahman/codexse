<?php

namespace App\Exceptions\Wallet;

class WalletOperationFailedException extends WalletException
{
    public function __construct(string $operation, string $reason, array $context = [])
    {
        parent::__construct(
            "Wallet operation '{$operation}' failed: {$reason}",
            'OPERATION_FAILED',
            array_merge($context, [
                'operation' => $operation,
                'reason' => $reason,
            ])
        );
    }
}
