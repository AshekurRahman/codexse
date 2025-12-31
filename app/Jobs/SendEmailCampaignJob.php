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

class SendEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 3600; // 1 hour

    public function __construct(
        public EmailCampaign $campaign
    ) {}

    public function handle(): void
    {
        $subscribers = NewsletterSubscriber::active()->get();

        foreach ($subscribers as $subscriber) {
            // Create log entry
            $log = EmailCampaignLog::create([
                'email_campaign_id' => $this->campaign->id,
                'newsletter_subscriber_id' => $subscriber->id,
                'status' => 'pending',
            ]);

            try {
                Mail::to($subscriber->email)->send(new CampaignMail($this->campaign, $subscriber));

                $log->markAsSent();

                $this->campaign->increment('sent_count');
            } catch (\Exception $e) {
                $log->markAsFailed($e->getMessage());

                $this->campaign->increment('failed_count');
            }
        }

        // Mark campaign as completed
        $this->campaign->update([
            'status' => 'sent',
            'completed_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'failed',
        ]);
    }
}
