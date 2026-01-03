<?php

namespace App\Exceptions\Wallet;

class DuplicateTransactionException extends WalletException
{
    protected mixed $cachedResponse;

    public function __construct(string $idempotencyKey, mixed $cachedResponse = null, array $context = [])
    {
        parent::__construct(
            'Transaction already processed with this idempotency key',
            'DUPLICATE_TRANSACTION',
            array_merge($context, ['idempotency_key' => $idempotencyKey])
        );

        $this->cachedResponse = $cachedResponse;
    }

    public function getCachedResponse(): mixed
    {
        return $this->cachedResponse;
    }
}
