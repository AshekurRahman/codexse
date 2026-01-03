<?php

namespace App\Console\Commands;

use App\Services\WalletService;
use Illuminate\Console\Command;

class CleanupWalletIdempotencyKeys extends Command
{
    protected $signature = 'wallet:cleanup-idempotency';
    protected $description = 'Clean up expired wallet idempotency keys';

    public function handle(WalletService $walletService): int
    {
        $this->info('Cleaning up expired wallet idempotency keys...');

        $cleaned = $walletService->cleanupIdempotencyKeys();

        $this->info("Cleaned up {$cleaned} expired idempotency key(s).");

        return Command::SUCCESS;
    }
}
