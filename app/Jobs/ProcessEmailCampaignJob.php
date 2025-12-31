<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 3600;

    public function __construct(
        public EmailCampaign $campaign
    ) {}

    public function handle(): void
    {
        // Refresh campaign state
        $this->campaign->refresh();

        // Check if campaign is still running
        if (!$this->campaign->isRunning()) {
            return;
        }

        // Check if we need to reset daily count (new day)
        if ($this->campaign->needsDailyReset()) {
            $this->campaign->resetDailyCount();
        }

        // Check if we can send today
        if (!$this->campaign->canSendToday()) {
            // Check if campaign is complete
            $this->checkCampaignCompletion();
            return;
        }

        // Get subscribers who haven't received this campaign yet
        $sentSubscriberIds = EmailCampaignLog::where('email_campaign_id', $this->campaign->id)
            ->pluck('newsletter_subscriber_id')
            ->toArray();

        $subscribers = NewsletterSubscriber::active()
            ->whereNotIn('id', $sentSubscriberIds)
            ->limit($this->campaign->remaining_today)
            ->get();

        if ($subscribers->isEmpty()) {
            // No more subscribers to send to
            $this->campaign->complete();
            return;
        }

        foreach ($subscribers as $subscriber) {
            // Double-check we can still send
            if (!$this->campaign->canSendToday()) {
                break;
            }

            $this->sendToSubscriber($subscriber);
        }

        // Check if campaign is complete
        $this->checkCampaignCompletion();

        $this->campaign->addLog("Batch complete. Sent: {$this->campaign->today_sent_count}/{$this->campaign->today_limit} today");
    }

    protected function sendToSubscriber(NewsletterSubscriber $subscriber): void
    {
        // Create log entry
        $log = EmailCampaignLog::create([
            'email_campaign_id' => $this->campaign->id,
            'newsletter_subscriber_id' => $subscriber->id,
            'status' => 'pending',
        ]);

        try {
            Mail::to($subscriber->email)->send(new CampaignMail($this->campaign, $subscriber));

            $log->markAsSent();
            $this->campaign->incrementSentCount();

        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
            $this->campaign->incrementFailedCount();

            $this->campaign->addLog("Failed to send to {$subscriber->email}: {$e->getMessage()}");
        }
    }

    protected function checkCampaignCompletion(): void
    {
        // Get total pending subscribers
        $sentCount = EmailCampaignLog::where('email_campaign_id', $this->campaign->id)->count();
        $totalSubscribers = NewsletterSubscriber::active()->count();

        if ($sentCount >= $totalSubscribers) {
            $this->campaign->complete();
            return;
        }

        // Check if campaign duration has ended
        if ($this->campaign->campaign_end_date && now()->startOfDay()->gt($this->campaign->campaign_end_date)) {
            $this->campaign->complete();
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->addLog("Job failed: {$exception->getMessage()}");
    }
}
