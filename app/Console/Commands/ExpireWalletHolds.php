<?php

namespace App\Console\Commands;

use App\Services\WalletService;
use Illuminate\Console\Command;

class ExpireWalletHolds extends Command
{
    protected $signature = 'wallet:expire-holds';
    protected $description = 'Expire and release old wallet holds that have passed their expiration time';

    public function handle(WalletService $walletService): int
    {
        $this->info('Processing expired wallet holds...');

        $expired = $walletService->expireOldHolds();

        $this->info("Expired {$expired} wallet hold(s).");

        return Command::SUCCESS;
    }
}
