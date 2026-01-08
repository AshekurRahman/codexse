<?php

namespace App\Console\Commands;

use App\Models\ProcessedWebhook;
use Illuminate\Console\Command;

class CleanupProcessedWebhooks extends Command
{
    protected $signature = 'webhooks:cleanup {--days=30 : Number of days to retain webhook records}';

    protected $description = 'Clean up old processed webhook records';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $count = ProcessedWebhook::cleanup($days);

        $this->info("Cleaned up {$count} webhook records older than {$days} days.");

        return Command::SUCCESS;
    }
}
