<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'currency',
        'is_active',
        'is_frozen',
        'last_transaction_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_frozen' => 'boolean',
        'last_transaction_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Accessors
    public function getFormattedBalanceAttribute(): string
    {
        return format_price($this->balance);
    }

    public function getFormattedPendingBalanceAttribute(): string
    {
        return format_price($this->pending_balance);
    }

    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->pending_balance;
    }

    public function getFormattedTotalBalanceAttribute(): string
    {
        return format_price($this->total_balance);
    }

    // Helper Methods
    public function canWithdraw(float $amount): bool
    {
        return $this->is_active && !$this->is_frozen && $this->balance >= $amount;
    }

    public function canPurchase(float $amount): bool
    {
        return $this->is_active && !$this->is_frozen && $this->balance >= $amount;
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    // Transaction Methods
    public function deposit(float $amount, ?string $description = null, ?string $paymentMethod = null, ?string $paymentId = null, $transactionable = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $paymentMethod, $paymentId, $transactionable) {
            $this->lockForUpdate();

            $balanceBefore = $this->balance;
            $this->balance += $amount;
            $this->last_transaction_at = now();
            $this->save();

            return $this->createTransaction(
                type: 'deposit',
                amount: $amount,
                balanceBefore: $balanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Wallet deposit',
                paymentMethod: $paymentMethod,
                paymentId: $paymentId,
                transactionable: $transactionable
            );
        });
    }

    public function withdraw(float $amount, ?string $description = null, ?string $paymentMethod = null, $transactionable = null): WalletTransaction
    {
        if (!$this->canWithdraw($amount)) {
            throw new \Exception('Insufficient balance or wallet is not available for withdrawal.');
        }

        return DB::transaction(function () use ($amount, $description, $paymentMethod, $transactionable) {
            $this->lockForUpdate();

            $balanceBefore = $this->balance;
            $this->balance -= $amount;
            $this->last_transaction_at = now();
            $this->save();

            return $this->createTransaction(
                type: 'withdrawal',
                amount: -$amount,
                balanceBefore: $balanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Wallet withdrawal',
                paymentMethod: $paymentMethod,
                transactionable: $transactionable
            );
        });
    }

    public function purchase(float $amount, ?string $description = null, $transactionable = null): WalletTransaction
    {
        if (!$this->canPurchase($amount)) {
            throw new \Exception('Insufficient balance or wallet is not available for purchase.');
        }

        return DB::transaction(function () use ($amount, $description, $transactionable) {
            $this->lockForUpdate();

            $balanceBefore = $this->balance;
            $this->balance -= $amount;
            $this->last_transaction_at = now();
            $this->save();

            return $this->createTransaction(
                type: 'purchase',
                amount: -$amount,
                balanceBefore: $balanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Purchase payment',
                transactionable: $transactionable
            );
        });
    }

    public function refund(float $amount, ?string $description = null, $transactionable = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $transactionable) {
            $this->lockForUpdate();

            $balanceBefore = $this->balance;
            $this->balance += $amount;
            $this->last_transaction_at = now();
            $this->save();

            return $this->createTransaction(
                type: 'refund',
                amount: $amount,
                balanceBefore: $balanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Refund',
                transactionable: $transactionable
            );
        });
    }

    public function addBonus(float $amount, ?string $description = null, ?array $metadata = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $metadata) {
            $this->lockForUpdate();

            $balanceBefore = $this->balance;
            $this->balance += $amount;
            $this->last_transaction_at = now();
            $this->save();

            return $this->createTransaction(
                type: 'bonus',
                amount: $amount,
                balanceBefore: $balanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Bonus credit',
                metadata: $metadata
            );
        });
    }

    public function transferTo(Wallet $recipient, float $amount, ?string $description = null): array
    {
        if (!$this->canWithdraw($amount)) {
            throw new \Exception('Insufficient balance for transfer.');
        }

        return DB::transaction(function () use ($recipient, $amount, $description) {
            $this->lockForUpdate();
            $recipient->lockForUpdate();

            // Deduct from sender
            $senderBalanceBefore = $this->balance;
            $this->balance -= $amount;
            $this->last_transaction_at = now();
            $this->save();

            $senderTransaction = $this->createTransaction(
                type: 'transfer_out',
                amount: -$amount,
                balanceBefore: $senderBalanceBefore,
                balanceAfter: $this->balance,
                description: $description ?? 'Transfer to ' . $recipient->user->name,
                metadata: ['recipient_wallet_id' => $recipient->id, 'recipient_user_id' => $recipient->user_id]
            );

            // Add to recipient
            $recipientBalanceBefore = $recipient->balance;
            $recipient->balance += $amount;
            $recipient->last_transaction_at = now();
            $recipient->save();

            $recipientTransaction = $recipient->createTransaction(
                type: 'transfer_in',
                amount: $amount,
                balanceBefore: $recipientBalanceBefore,
                balanceAfter: $recipient->balance,
                description: $description ?? 'Transfer from ' . $this->user->name,
                metadata: ['sender_wallet_id' => $this->id, 'sender_user_id' => $this->user_id]
            );

            return [
                'sender_transaction' => $senderTransaction,
                'recipient_transaction' => $recipientTransaction,
            ];
        });
    }

    public function createTransaction(
        string $type,
        float $amount,
        float $balanceBefore,
        float $balanceAfter,
        ?string $description = null,
        ?string $paymentMethod = null,
        ?string $paymentId = null,
        $transactionable = null,
        ?array $metadata = null,
        string $status = 'completed'
    ): WalletTransaction {
        $transaction = new WalletTransaction([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'reference' => $this->generateReference(),
            'description' => $description,
            'metadata' => $metadata,
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        if ($transactionable) {
            $transaction->transactionable_type = get_class($transactionable);
            $transaction->transactionable_id = $transactionable->id;
        }

        $transaction->save();

        return $transaction;
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'WTX-' . strtoupper(Str::random(12));
        } while (WalletTransaction::where('reference', $reference)->exists());

        return $reference;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_frozen', false);
    }

    public function scopeWithPositiveBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    // Static Methods
    public static function getOrCreateForUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );
    }
}
