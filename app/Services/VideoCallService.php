<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\VideoCall;
use Illuminate\Support\Str;

class VideoCallService
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = Setting::get('video_call_provider', 'jitsi');
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $this->config = [
            'provider' => $this->provider,
            'agora' => [
                'app_id' => Setting::get('agora_app_id', ''),
                'app_certificate' => Setting::get('agora_app_certificate', ''),
            ],
            'twilio' => [
                'account_sid' => Setting::get('twilio_account_sid', ''),
                'api_key' => Setting::get('twilio_api_key', ''),
                'api_secret' => Setting::get('twilio_api_secret', ''),
            ],
            'daily' => [
                'api_key' => Setting::get('daily_api_key', ''),
                'domain' => Setting::get('daily_domain', ''),
            ],
            'jitsi' => [
                'domain' => Setting::get('jitsi_domain', 'meet.jit.si'),
                'app_id' => Setting::get('jitsi_app_id', ''),
                'secret' => Setting::get('jitsi_secret', ''),
            ],
        ];
    }

    /**
     * Create a new video call
     */
    public function createCall(User $host, ?User $participant = null, array $options = []): VideoCall
    {
        $roomId = 'room_' . Str::random(16);

        $call = VideoCall::create([
            'room_id' => $roomId,
            'host_id' => $host->id,
            'participant_id' => $participant?->id,
            'conversation_id' => $options['conversation_id'] ?? null,
            'service_order_id' => $options['service_order_id'] ?? null,
            'status' => isset($options['scheduled_at']) ? 'scheduled' : 'pending',
            'type' => $options['type'] ?? 'video',
            'scheduled_at' => $options['scheduled_at'] ?? null,
            'provider' => $this->provider,
            'notes' => $options['notes'] ?? null,
            'provider_data' => $this->generateProviderData($roomId, $host),
        ]);

        return $call;
    }

    /**
     * Generate provider-specific data (tokens, URLs, etc.)
     */
    protected function generateProviderData(string $roomId, User $host): array
    {
        return match ($this->provider) {
            'jitsi' => $this->generateJitsiData($roomId, $host),
            'agora' => $this->generateAgoraData($roomId, $host),
            'twilio' => $this->generateTwilioData($roomId, $host),
            'daily' => $this->generateDailyData($roomId),
            default => ['room_id' => $roomId],
        };
    }

    protected function generateJitsiData(string $roomId, User $host): array
    {
        $domain = $this->config['jitsi']['domain'];
        $roomName = 'codexse-' . $roomId;

        return [
            'domain' => $domain,
            'room_name' => $roomName,
            'join_url' => "https://{$domain}/{$roomName}",
            'display_name' => $host->name,
        ];
    }

    protected function generateAgoraData(string $roomId, User $host): array
    {
        // Note: In production, you'd generate proper Agora tokens here
        return [
            'app_id' => $this->config['agora']['app_id'],
            'channel' => $roomId,
            'uid' => $host->id,
            // Token generation would happen here
        ];
    }

    protected function generateTwilioData(string $roomId, User $host): array
    {
        // Note: In production, you'd create a Twilio room and generate access tokens
        return [
            'room_name' => $roomId,
            'identity' => $host->email,
        ];
    }

    protected function generateDailyData(string $roomId): array
    {
        $domain = $this->config['daily']['domain'];

        return [
            'room_name' => $roomId,
            'join_url' => "https://{$domain}/{$roomId}",
        ];
    }

    /**
     * Get join URL for a call
     */
    public function getJoinUrl(VideoCall $call): string
    {
        $providerData = $call->provider_data ?? [];

        return match ($call->provider) {
            'jitsi' => $providerData['join_url'] ?? '',
            'daily' => $providerData['join_url'] ?? '',
            'agora' => route('video-call.room', ['call' => $call->room_id]),
            'twilio' => route('video-call.room', ['call' => $call->room_id]),
            default => route('video-call.room', ['call' => $call->room_id]),
        };
    }

    /**
     * Start a call
     */
    public function startCall(VideoCall $call): void
    {
        $call->start();
    }

    /**
     * End a call
     */
    public function endCall(VideoCall $call): void
    {
        $call->end();
    }

    /**
     * Check if video calls are enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('video_calls_enabled', false);
    }

    /**
     * Get current provider
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get provider configuration
     */
    public function getProviderConfig(): array
    {
        return $this->config[$this->provider] ?? [];
    }
}
