<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    /**
     * Track email opens via a 1x1 pixel image.
     */
    public function trackOpen(string $hash): Response
    {
        $data = $this->decodeHash($hash);

        if ($data) {
            $log = EmailCampaignLog::where('email_campaign_id', $data['campaign_id'])
                ->where('newsletter_subscriber_id', $data['subscriber_id'])
                ->first();

            if ($log && !$log->opened_at) {
                $log->markAsOpened();

                // Update campaign open count
                EmailCampaign::where('id', $data['campaign_id'])
                    ->increment('opened_count');
            }
        }

        // Return a 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($pixel),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Track link clicks and redirect.
     */
    public function trackClick(Request $request, string $hash): \Illuminate\Http\RedirectResponse
    {
        $url = $request->query('url', config('app.url'));
        $data = $this->decodeHash($hash);

        if ($data) {
            $log = EmailCampaignLog::where('email_campaign_id', $data['campaign_id'])
                ->where('newsletter_subscriber_id', $data['subscriber_id'])
                ->first();

            if ($log && !$log->clicked_at) {
                $log->markAsClicked();

                // Update campaign click count
                EmailCampaign::where('id', $data['campaign_id'])
                    ->increment('clicked_count');
            }
        }

        return redirect()->away($url);
    }

    /**
     * Generate tracking hash.
     */
    public static function generateHash(int $campaignId, int $subscriberId): string
    {
        $data = json_encode([
            'c' => $campaignId,
            's' => $subscriberId,
        ]);

        return base64_encode($data);
    }

    /**
     * Decode tracking hash.
     */
    private function decodeHash(string $hash): ?array
    {
        try {
            $data = json_decode(base64_decode($hash), true);

            if (isset($data['c']) && isset($data['s'])) {
                return [
                    'campaign_id' => $data['c'],
                    'subscriber_id' => $data['s'],
                ];
            }
        } catch (\Exception $e) {
            // Invalid hash
        }

        return null;
    }
}
