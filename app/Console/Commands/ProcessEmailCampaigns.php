<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEmailCampaignJob;
use App\Models\EmailCampaign;
use Illuminate\Console\Command;

class ProcessEmailCampaigns extends Command
{
    protected $signature = 'campaigns:process';
    protected $description = 'Process all running email campaigns and dispatch their sending jobs';

    public function handle(): int
    {
        $this->info('Processing email campaigns...');

        // Get all running campaigns
        $campaigns = EmailCampaign::running()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No running campaigns found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$campaigns->count()} running campaign(s).");

        foreach ($campaigns as $campaign) {
            // Check if campaign needs daily reset
            if ($campaign->needsDailyReset()) {
                $campaign->resetDailyCount();
                $this->info("- {$campaign->name}: Reset daily count for Day {$campaign->current_day}");
            }

            // Check if campaign can send today
            if ($campaign->canSendToday()) {
                dispatch(new ProcessEmailCampaignJob($campaign));
                $this->info("- {$campaign->name}: Dispatched processing job ({$campaign->remaining_today} remaining today)");
            } else {
                // Check if campaign duration has ended
                if ($campaign->campaign_end_date && now()->startOfDay()->gt($campaign->campaign_end_date)) {
                    $campaign->complete();
                    $this->info("- {$campaign->name}: Completed (duration ended)");
                } else {
                    $this->info("- {$campaign->name}: Daily limit reached ({$campaign->today_sent_count}/{$campaign->today_limit})");
                }
            }
        }

        $this->info('Campaign processing complete.');

        return Command::SUCCESS;
    }
}
