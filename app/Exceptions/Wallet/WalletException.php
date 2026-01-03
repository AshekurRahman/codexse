<?php

namespace App\Exceptions\Wallet;

use Exception;

abstract class WalletException extends Exception
{
    protected string $errorCode;
    protected array $context = [];

    public function __construct(string $message, string $errorCode, array $context = [], ?Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function toArray(): array
    {
        return [
            'error' => $this->errorCode,
            'message' => $this->getMessage(),
            'context' => $this->context,
        ];
    }
}
