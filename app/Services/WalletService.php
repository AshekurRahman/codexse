<?php

namespace App\Services;

use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Exceptions\Wallet\HoldExpiredException;
use App\Exceptions\Wallet\HoldNotFoundException;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Exceptions\Wallet\WalletException;
use App\Exceptions\Wallet\WalletFrozenException;
use App\Exceptions\Wallet\WalletInactiveException;
use App\Exceptions\Wallet\WalletOperationFailedException;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHold;
use App\Models\WalletIdempotencyKey;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletService
{
    protected int $holdExpirationMinutes = 30;
    protected int $maxRetries = 3;
    protected int $retryDelayMs = 100;
    protected int $idempotencyKeyTtlHours = 24;

    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Load settings from database.
     */
    protected function loadSettings(): void
    {
        $this->holdExpirationMinutes = (int) Setting::get('wallet_hold_expiration_minutes', 30);
        $this->maxRetries = (int) Setting::get('wallet_max_retries', 3);
        $this->idempotencyKeyTtlHours = (int) Setting::get('wallet_idempotency_key_ttl_hours', 24);
    }

    /**
     * Check if wallet payments are configured (always true for internal wallet).
     */
    public function isConfigured(): bool
    {
        return true;
    }

    /**
     * Check if wallet payments are enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) Setting::get('wallet_payments_enabled', true);
    }

    // ========================================
    // Balance Operations
    // ========================================

    /**
     * Get the total balance of a wallet.
     */
    public function getBalance(Wallet $wallet): float
    {
        return (float) $wallet->balance;
    }

    /**
     * Get the available balance (total minus held).
     */
    public function getAvailableBalance(Wallet $wallet): float
    {
        return max(0, $wallet->balance - ($wallet->held_balance ?? 0));
    }

    /**
     * Get the held balance.
     */
    public function getHeldBalance(Wallet $wallet): float
    {
        return (float) ($wallet->held_balance ?? 0);
    }

    // ========================================
    // Hold/Release Mechanism
    // ========================================

    /**
     * Hold funds in wallet (like Stripe authorize).
     *
     * @throws WalletException
     */
    public function holdFunds(
        Wallet $wallet,
        float $amount,
        string $idempotencyKey,
        ?string $description = null,
        $holdable = null,
        ?array $metadata = null,
        ?int $expirationMinutes = null
    ): WalletHold {
        Log::info('WalletService: Attempting to hold funds', [
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'idempotency_key' => $idempotencyKey,
        ]);

        // Check idempotency first
        $cached = $this->checkIdempotency($wallet, $idempotencyKey, WalletIdempotencyKey::OPERATION_HOLD);
        if ($cached && isset($cached['hold_id'])) {
            Log::info('WalletService: Returning cached hold from idempotency', [
                'hold_id' => $cached['hold_id'],
            ]);
            return WalletHold::findOrFail($cached['hold_id']);
        }

        return $this->executeWithRetry(function () use (
            $wallet, $amount, $idempotencyKey, $description,
            $holdable, $metadata, $expirationMinutes
        ) {
            return DB::transaction(function () use (
                $wallet, $amount, $idempotencyKey, $description,
                $holdable, $metadata, $expirationMinutes
            ) {
                // Lock wallet row for update (pessimistic locking)
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

                // Validate wallet state
                $this->validateWallet($wallet);

                // Check available balance
                $availableBalance = $this->getAvailableBalance($wallet);
                if ($availableBalance < $amount) {
                    throw new InsufficientBalanceException($amount, $availableBalance, [
                        'wallet_id' => $wallet->id,
                    ]);
                }

                // Update held balance
                $wallet->held_balance = ($wallet->held_balance ?? 0) + $amount;
                $wallet->save();

                // Create hold record
                $hold = WalletHold::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'idempotency_key' => $idempotencyKey,
                    'amount' => $amount,
                    'balance_before' => $wallet->balance,
                    'status' => WalletHold::STATUS_PENDING,
                    'holdable_type' => $holdable ? get_class($holdable) : null,
                    'holdable_id' => $holdable?->id,
                    'description' => $description,
                    'metadata' => $metadata,
                    'expires_at' => now()->addMinutes($expirationMinutes ?? $this->holdExpirationMinutes),
                ]);

                // Store idempotency record
                $this->storeIdempotency(
                    $wallet,
                    $idempotencyKey,
                    WalletIdempotencyKey::OPERATION_HOLD,
                    null,
                    $hold,
                    ['hold_id' => $hold->id]
                );

                Log::info('WalletService: Funds held successfully', [
                    'hold_id' => $hold->id,
                    'wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'expires_at' => $hold->expires_at->toDateTimeString(),
                ]);

                return $hold;
            });
        });
    }

    /**
     * Capture held funds (like Stripe capture).
     *
     * @throws WalletException
     */
    public function captureFunds(
        WalletHold $hold,
        ?float $amount = null,
        ?string $description = null,
        $transactionable = null
    ): WalletTransaction {
        $captureAmount = $amount ?? $hold->amount;

        Log::info('WalletService: Attempting to capture funds', [
            'hold_id' => $hold->id,
            'capture_amount' => $captureAmount,
            'hold_amount' => $hold->amount,
        ]);

        return $this->executeWithRetry(function () use ($hold, $captureAmount, $description, $transactionable) {
            return DB::transaction(function () use ($hold, $captureAmount, $description, $transactionable) {
                // Lock both hold and wallet
                $hold = WalletHold::where('id', $hold->id)->lockForUpdate()->firstOrFail();
                $wallet = Wallet::where('id', $hold->wallet_id)->lockForUpdate()->firstOrFail();

                // Validate hold state
                if ($hold->status !== WalletHold::STATUS_PENDING) {
                    throw new WalletOperationFailedException(
                        'capture',
                        "Hold is not in pending status (current: {$hold->status})",
                        ['hold_id' => $hold->id]
                    );
                }

                if ($hold->isExpired()) {
                    throw new HoldExpiredException($hold->id, [
                        'expires_at' => $hold->expires_at->toDateTimeString(),
                    ]);
                }

                if ($captureAmount > $hold->amount) {
                    throw new WalletOperationFailedException(
                        'capture',
                        "Capture amount ({$captureAmount}) exceeds hold amount ({$hold->amount})",
                        ['hold_id' => $hold->id]
                    );
                }

                $balanceBefore = $wallet->balance;

                // Deduct from actual balance
                $wallet->balance -= $captureAmount;
                // Release held amount (full hold amount, not capture amount)
                $wallet->held_balance -= $hold->amount;
                $wallet->last_transaction_at = now();
                $wallet->save();

                // Create transaction
                $transaction = $wallet->createTransaction(
                    type: WalletTransaction::TYPE_PURCHASE,
                    amount: -$captureAmount,
                    balanceBefore: $balanceBefore,
                    balanceAfter: $wallet->balance,
                    description: $description ?? $hold->description ?? 'Wallet payment',
                    transactionable: $transactionable ?? $hold->holdable,
                    metadata: ['hold_id' => $hold->id]
                );

                // Update hold status
                $newStatus = $captureAmount >= $hold->amount
                    ? WalletHold::STATUS_CAPTURED
                    : WalletHold::STATUS_CAPTURED; // Treat partial as captured

                $hold->update([
                    'status' => $newStatus,
                    'captured_at' => now(),
                    'captured_transaction_id' => $transaction->id,
                    'metadata' => array_merge($hold->metadata ?? [], [
                        'captured_amount' => $captureAmount,
                        'partial_capture' => $captureAmount < $hold->amount,
                    ]),
                ]);

                // Log if partial capture
                if ($captureAmount < $hold->amount) {
                    $releasedAmount = $hold->amount - $captureAmount;
                    Log::info('WalletService: Partial capture completed', [
                        'hold_id' => $hold->id,
                        'captured' => $captureAmount,
                        'released' => $releasedAmount,
                    ]);
                }

                Log::info('WalletService: Funds captured successfully', [
                    'hold_id' => $hold->id,
                    'transaction_id' => $transaction->id,
                    'transaction_ref' => $transaction->reference,
                    'amount' => $captureAmount,
                ]);

                return $transaction;
            });
        });
    }

    /**
     * Release held funds back to available balance.
     */
    public function releaseFunds(WalletHold $hold, ?string $reason = null): bool
    {
        Log::info('WalletService: Attempting to release funds', [
            'hold_id' => $hold->id,
            'amount' => $hold->amount,
            'reason' => $reason,
        ]);

        try {
            return DB::transaction(function () use ($hold, $reason) {
                $hold = WalletHold::where('id', $hold->id)->lockForUpdate()->firstOrFail();
                $wallet = Wallet::where('id', $hold->wallet_id)->lockForUpdate()->firstOrFail();

                if ($hold->status !== WalletHold::STATUS_PENDING) {
                    Log::warning('WalletService: Cannot release - hold not pending', [
                        'hold_id' => $hold->id,
                        'status' => $hold->status,
                    ]);
                    return false;
                }

                // Release held amount
                $wallet->held_balance = max(0, ($wallet->held_balance ?? 0) - $hold->amount);
                $wallet->save();

                $hold->update([
                    'status' => WalletHold::STATUS_RELEASED,
                    'released_at' => now(),
                    'metadata' => array_merge($hold->metadata ?? [], ['release_reason' => $reason]),
                ]);

                Log::info('WalletService: Funds released successfully', [
                    'hold_id' => $hold->id,
                    'amount' => $hold->amount,
                    'reason' => $reason,
                ]);

                return true;
            });
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to release funds', [
                'hold_id' => $hold->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // ========================================
    // Direct Operations
    // ========================================

    /**
     * Direct purchase (deduct immediately without hold).
     *
     * @throws WalletException
     */
    public function purchase(
        Wallet $wallet,
        float $amount,
        string $idempotencyKey,
        ?string $description = null,
        $transactionable = null,
        ?array $metadata = null
    ): WalletTransaction {
        Log::info('WalletService: Processing direct purchase', [
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'idempotency_key' => $idempotencyKey,
        ]);

        // Check idempotency first
        $cached = $this->checkIdempotency($wallet, $idempotencyKey, WalletIdempotencyKey::OPERATION_PURCHASE);
        if ($cached && isset($cached['transaction_id'])) {
            Log::info('WalletService: Returning cached transaction from idempotency', [
                'transaction_id' => $cached['transaction_id'],
            ]);
            return WalletTransaction::findOrFail($cached['transaction_id']);
        }

        return $this->executeWithRetry(function () use (
            $wallet, $amount, $idempotencyKey, $description, $transactionable, $metadata
        ) {
            return DB::transaction(function () use (
                $wallet, $amount, $idempotencyKey, $description, $transactionable, $metadata
            ) {
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

                $this->validateWallet($wallet);

                $availableBalance = $this->getAvailableBalance($wallet);
                if ($availableBalance < $amount) {
                    throw new InsufficientBalanceException($amount, $availableBalance, [
                        'wallet_id' => $wallet->id,
                    ]);
                }

                $balanceBefore = $wallet->balance;
                $wallet->balance -= $amount;
                $wallet->last_transaction_at = now();
                $wallet->save();

                $transaction = $wallet->createTransaction(
                    type: WalletTransaction::TYPE_PURCHASE,
                    amount: -$amount,
                    balanceBefore: $balanceBefore,
                    balanceAfter: $wallet->balance,
                    description: $description ?? 'Wallet payment',
                    transactionable: $transactionable,
                    metadata: $metadata
                );

                $this->storeIdempotency(
                    $wallet,
                    $idempotencyKey,
                    WalletIdempotencyKey::OPERATION_PURCHASE,
                    $transaction,
                    null,
                    ['transaction_id' => $transaction->id]
                );

                Log::info('WalletService: Direct purchase successful', [
                    'wallet_id' => $wallet->id,
                    'transaction_id' => $transaction->id,
                    'transaction_ref' => $transaction->reference,
                    'amount' => $amount,
                ]);

                return $transaction;
            });
        });
    }

    /**
     * Refund to wallet.
     *
     * @throws WalletException
     */
    public function refund(
        Wallet $wallet,
        float $amount,
        string $idempotencyKey,
        ?string $description = null,
        $transactionable = null,
        ?array $metadata = null
    ): WalletTransaction {
        Log::info('WalletService: Processing refund', [
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'idempotency_key' => $idempotencyKey,
        ]);

        // Check idempotency first
        $cached = $this->checkIdempotency($wallet, $idempotencyKey, WalletIdempotencyKey::OPERATION_REFUND);
        if ($cached && isset($cached['transaction_id'])) {
            Log::info('WalletService: Returning cached refund from idempotency', [
                'transaction_id' => $cached['transaction_id'],
            ]);
            return WalletTransaction::findOrFail($cached['transaction_id']);
        }

        return $this->executeWithRetry(function () use (
            $wallet, $amount, $idempotencyKey, $description, $transactionable, $metadata
        ) {
            return DB::transaction(function () use (
                $wallet, $amount, $idempotencyKey, $description, $transactionable, $metadata
            ) {
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

                $balanceBefore = $wallet->balance;
                $wallet->balance += $amount;
                $wallet->last_transaction_at = now();
                $wallet->save();

                $transaction = $wallet->createTransaction(
                    type: WalletTransaction::TYPE_REFUND,
                    amount: $amount,
                    balanceBefore: $balanceBefore,
                    balanceAfter: $wallet->balance,
                    description: $description ?? 'Refund',
                    transactionable: $transactionable,
                    metadata: $metadata
                );

                $this->storeIdempotency(
                    $wallet,
                    $idempotencyKey,
                    WalletIdempotencyKey::OPERATION_REFUND,
                    $transaction,
                    null,
                    ['transaction_id' => $transaction->id]
                );

                Log::info('WalletService: Refund successful', [
                    'wallet_id' => $wallet->id,
                    'transaction_id' => $transaction->id,
                    'transaction_ref' => $transaction->reference,
                    'amount' => $amount,
                ]);

                return $transaction;
            });
        });
    }

    // ========================================
    // Partial Payment Support
    // ========================================

    /**
     * Calculate how much can be paid with wallet vs secondary method.
     */
    public function calculatePartialPayment(Wallet $wallet, float $totalAmount): array
    {
        $availableBalance = $this->getAvailableBalance($wallet);

        if ($availableBalance <= 0) {
            return [
                'wallet_amount' => 0,
                'remaining_amount' => $totalAmount,
                'can_use_wallet' => false,
                'covers_full_amount' => false,
            ];
        }

        $walletAmount = min($availableBalance, $totalAmount);
        $remainingAmount = $totalAmount - $walletAmount;

        return [
            'wallet_amount' => round($walletAmount, 2),
            'remaining_amount' => round($remainingAmount, 2),
            'can_use_wallet' => true,
            'covers_full_amount' => $remainingAmount <= 0,
        ];
    }

    /**
     * Process a partial wallet payment (hold funds for later capture).
     *
     * @throws WalletException
     */
    public function processPartialWalletPayment(
        Wallet $wallet,
        float $walletAmount,
        float $totalAmount,
        string $idempotencyKey,
        Order $order
    ): WalletHold {
        Log::info('WalletService: Processing partial wallet payment', [
            'wallet_id' => $wallet->id,
            'wallet_amount' => $walletAmount,
            'total_amount' => $totalAmount,
            'order_id' => $order->id,
        ]);

        return $this->holdFunds(
            wallet: $wallet,
            amount: $walletAmount,
            idempotencyKey: $idempotencyKey,
            description: 'Partial payment: Order #' . $order->order_number,
            holdable: $order,
            metadata: [
                'total_amount' => $totalAmount,
                'remaining_amount' => $totalAmount - $walletAmount,
                'is_partial' => true,
            ],
            expirationMinutes: $this->holdExpirationMinutes
        );
    }

    // ========================================
    // Idempotency
    // ========================================

    /**
     * Check if an idempotency key exists and return cached response.
     */
    public function checkIdempotency(Wallet $wallet, string $key, string $operation): ?array
    {
        $record = WalletIdempotencyKey::findValid($key, $wallet->id);

        if ($record && $record->operation === $operation) {
            return $record->response;
        }

        return null;
    }

    /**
     * Store an idempotency key with response.
     */
    public function storeIdempotency(
        Wallet $wallet,
        string $key,
        string $operation,
        ?WalletTransaction $transaction = null,
        ?WalletHold $hold = null,
        array $response = []
    ): void {
        WalletIdempotencyKey::updateOrCreate(
            [
                'key' => $key,
                'wallet_id' => $wallet->id,
            ],
            [
                'operation' => $operation,
                'transaction_id' => $transaction?->id,
                'hold_id' => $hold?->id,
                'response' => $response,
                'expires_at' => now()->addHours($this->idempotencyKeyTtlHours),
            ]
        );
    }

    // ========================================
    // Validation
    // ========================================

    /**
     * Validate wallet state for transactions.
     *
     * @throws WalletException
     */
    public function validateWallet(Wallet $wallet): void
    {
        if (!$wallet->is_active) {
            throw new WalletInactiveException($wallet->id);
        }

        if ($wallet->is_frozen) {
            throw new WalletFrozenException($wallet->id);
        }
    }

    /**
     * Check if wallet can make a purchase.
     */
    public function canPurchase(Wallet $wallet, float $amount): bool
    {
        try {
            $this->validateWallet($wallet);
            return $this->getAvailableBalance($wallet) >= $amount;
        } catch (WalletException) {
            return false;
        }
    }

    /**
     * Check if wallet can hold an amount.
     */
    public function canHold(Wallet $wallet, float $amount): bool
    {
        return $this->canPurchase($wallet, $amount);
    }

    // ========================================
    // Retry Logic
    // ========================================

    /**
     * Execute operation with retry on deadlock.
     *
     * @throws WalletException
     */
    public function executeWithRetry(callable $operation, ?int $maxRetries = null): mixed
    {
        $maxRetries = $maxRetries ?? $this->maxRetries;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return $operation();
            } catch (\Illuminate\Database\QueryException $e) {
                // Only retry on deadlock or lock timeout
                if (Str::contains($e->getMessage(), ['Deadlock', 'Lock wait timeout', 'try restarting transaction'])) {
                    $lastException = $e;
                    $attempt++;
                    $delay = $this->retryDelayMs * pow(2, $attempt); // Exponential backoff
                    usleep($delay * 1000);

                    Log::warning('WalletService: Retrying after deadlock', [
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries,
                        'delay_ms' => $delay,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
                throw $e;
            }
        }

        Log::error('WalletService: Operation failed after retries', [
            'attempts' => $attempt,
            'last_error' => $lastException?->getMessage(),
        ]);

        throw new WalletOperationFailedException(
            'retry',
            "Operation failed after {$maxRetries} attempts",
            ['last_error' => $lastException?->getMessage()]
        );
    }

    // ========================================
    // Cleanup Operations
    // ========================================

    /**
     * Expire old pending holds and release funds.
     */
    public function expireOldHolds(): int
    {
        $expired = 0;

        WalletHold::expired()->chunk(100, function ($holds) use (&$expired) {
            foreach ($holds as $hold) {
                try {
                    DB::transaction(function () use ($hold) {
                        $hold = WalletHold::where('id', $hold->id)->lockForUpdate()->first();
                        if (!$hold || $hold->status !== WalletHold::STATUS_PENDING) {
                            return;
                        }

                        $wallet = Wallet::where('id', $hold->wallet_id)->lockForUpdate()->first();
                        if ($wallet) {
                            $wallet->held_balance = max(0, ($wallet->held_balance ?? 0) - $hold->amount);
                            $wallet->save();
                        }

                        $hold->update([
                            'status' => WalletHold::STATUS_EXPIRED,
                            'released_at' => now(),
                            'metadata' => array_merge($hold->metadata ?? [], [
                                'expired_reason' => 'auto_expired',
                            ]),
                        ]);
                    });

                    $expired++;

                    Log::info('WalletService: Expired hold', [
                        'hold_id' => $hold->id,
                        'amount' => $hold->amount,
                    ]);
                } catch (\Exception $e) {
                    Log::error('WalletService: Failed to expire hold', [
                        'hold_id' => $hold->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        if ($expired > 0) {
            Log::info("WalletService: Expired {$expired} wallet holds");
        }

        return $expired;
    }

    /**
     * Clean up old idempotency keys.
     */
    public function cleanupIdempotencyKeys(): int
    {
        $deleted = WalletIdempotencyKey::expired()->delete();

        if ($deleted > 0) {
            Log::info("WalletService: Cleaned up {$deleted} expired idempotency keys");
        }

        return $deleted;
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Generate an idempotency key for checkout.
     */
    public function generateCheckoutIdempotencyKey(Order $order, int $userId, float $amount): string
    {
        return 'checkout_' . $order->order_number . '_' . md5(json_encode([
            'user_id' => $userId,
            'amount' => $amount,
            'timestamp' => floor(time() / 60), // 1-minute window
        ]));
    }

    /**
     * Find a hold by ID.
     *
     * @throws HoldNotFoundException
     */
    public function findHold(int $holdId): WalletHold
    {
        $hold = WalletHold::find($holdId);

        if (!$hold) {
            throw new HoldNotFoundException($holdId);
        }

        return $hold;
    }

    /**
     * Get or create wallet for user.
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::getOrCreateForUser($user);
    }
}
