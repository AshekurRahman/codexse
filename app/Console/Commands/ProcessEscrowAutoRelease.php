<?php

namespace App\Console\Commands;

use App\Services\EscrowService;
use Illuminate\Console\Command;

class ProcessEscrowAutoRelease extends Command
{
    protected $signature = 'escrow:auto-release';
    protected $description = 'Process automatic release of escrow transactions after the approval period';

    public function handle(EscrowService $escrowService): int
    {
        $this->info('Processing escrow auto-releases...');

        $released = $escrowService->processAutoRelease();

        $this->info("Released {$released} escrow transaction(s).");

        return Command::SUCCESS;
    }
}
