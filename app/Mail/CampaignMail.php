<?php

namespace App\Mail;

use App\Http\Controllers\EmailTrackingController;
use App\Models\EmailCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public NewsletterSubscriber $subscriber
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    public function content(): Content
    {
        $trackingHash = EmailTrackingController::generateHash(
            $this->campaign->id,
            $this->subscriber->id
        );

        return new Content(
            view: 'emails.campaign',
            with: [
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'content' => $this->wrapLinksForTracking($this->campaign->content, $trackingHash),
                'previewText' => $this->campaign->preview_text,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->token),
                'trackingPixelUrl' => route('email.track.open', $trackingHash),
            ],
        );
    }

    /**
     * Wrap links in content for click tracking.
     */
    private function wrapLinksForTracking(string $content, string $hash): string
    {
        // Match href attributes in anchor tags
        return preg_replace_callback(
            '/<a\s+([^>]*href=["\'])([^"\']+)(["\'][^>]*)>/i',
            function ($matches) use ($hash) {
                $originalUrl = $matches[2];

                // Skip tracking for unsubscribe links and anchor links
                if (str_contains($originalUrl, 'unsubscribe') || str_starts_with($originalUrl, '#')) {
                    return $matches[0];
                }

                $trackedUrl = route('email.track.click', $hash) . '?url=' . urlencode($originalUrl);
                return '<a ' . $matches[1] . $trackedUrl . $matches[3] . '>';
            },
            $content
        );
    }
}
