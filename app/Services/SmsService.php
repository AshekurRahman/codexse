<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $provider;
    protected array $config;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = (bool) Setting::get('sms_enabled', false);
        $this->provider = Setting::get('sms_provider', 'twilio');
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $this->config = [
            'twilio' => [
                'account_sid' => Setting::get('twilio_sms_account_sid', ''),
                'auth_token' => Setting::get('twilio_sms_auth_token', ''),
                'from_number' => Setting::get('twilio_sms_from_number', ''),
            ],
            'nexmo' => [
                'api_key' => Setting::get('nexmo_api_key', ''),
                'api_secret' => Setting::get('nexmo_api_secret', ''),
                'from_number' => Setting::get('nexmo_from_number', ''),
            ],
            'sns' => [
                'access_key' => Setting::get('aws_sns_access_key', ''),
                'secret_key' => Setting::get('aws_sns_secret_key', ''),
                'region' => Setting::get('aws_sns_region', 'us-east-1'),
            ],
        ];
    }

    /**
     * Send an SMS
     */
    public function send(string $phoneNumber, string $message, string $type = 'general', ?User $user = null): SmsLog
    {
        // Create log entry
        $log = SmsLog::create([
            'user_id' => $user?->id,
            'phone_number' => $phoneNumber,
            'type' => $type,
            'message' => $message,
            'status' => 'pending',
            'provider' => $this->provider,
        ]);

        if (!$this->enabled) {
            $log->markAsFailed('SMS service is disabled');
            return $log;
        }

        try {
            $result = match ($this->provider) {
                'twilio' => $this->sendViaTwilio($phoneNumber, $message),
                'nexmo' => $this->sendViaNexmo($phoneNumber, $message),
                'sns' => $this->sendViaSns($phoneNumber, $message),
                default => throw new \Exception('Unknown SMS provider'),
            };

            $log->update([
                'status' => 'sent',
                'provider_message_id' => $result['message_id'] ?? null,
                'provider_response' => $result,
                'cost' => $result['cost'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            $log->markAsFailed($e->getMessage());
        }

        return $log;
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $to, string $message): array
    {
        $config = $this->config['twilio'];

        $response = Http::withBasicAuth($config['account_sid'], $config['auth_token'])
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$config['account_sid']}/Messages.json", [
                'To' => $to,
                'From' => $config['from_number'],
                'Body' => $message,
            ]);

        if (!$response->successful()) {
            throw new \Exception($response->json('message', 'Twilio API error'));
        }

        $data = $response->json();

        return [
            'message_id' => $data['sid'] ?? null,
            'status' => $data['status'] ?? null,
            'cost' => $data['price'] ?? null,
        ];
    }

    /**
     * Send SMS via Nexmo/Vonage
     */
    protected function sendViaNexmo(string $to, string $message): array
    {
        $config = $this->config['nexmo'];

        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => $config['api_key'],
            'api_secret' => $config['api_secret'],
            'to' => $to,
            'from' => $config['from_number'],
            'text' => $message,
        ]);

        $data = $response->json();

        if (isset($data['messages'][0]['status']) && $data['messages'][0]['status'] !== '0') {
            throw new \Exception($data['messages'][0]['error-text'] ?? 'Nexmo API error');
        }

        return [
            'message_id' => $data['messages'][0]['message-id'] ?? null,
            'status' => 'sent',
            'cost' => $data['messages'][0]['message-price'] ?? null,
        ];
    }

    /**
     * Send SMS via AWS SNS
     */
    protected function sendViaSns(string $to, string $message): array
    {
        // In production, you'd use the AWS SDK here
        // This is a simplified placeholder

        $config = $this->config['sns'];

        // AWS SDK integration would go here
        // $client = new SnsClient([...]);
        // $result = $client->publish([...]);

        return [
            'message_id' => 'sns_' . uniqid(),
            'status' => 'sent',
        ];
    }

    /**
     * Send order confirmation SMS
     */
    public function sendOrderConfirmation(User $user, string $orderNumber): ?SmsLog
    {
        if (!$user->phone || !Setting::get('sms_order_confirmation', true)) {
            return null;
        }

        $message = "Your order #{$orderNumber} has been confirmed! Thank you for your purchase.";

        return $this->send($user->phone, $message, 'order_confirmation', $user);
    }

    /**
     * Send order shipped SMS
     */
    public function sendOrderShipped(User $user, string $orderNumber, ?string $trackingNumber = null): ?SmsLog
    {
        if (!$user->phone || !Setting::get('sms_order_shipped', true)) {
            return null;
        }

        $message = "Your order #{$orderNumber} has been shipped!";
        if ($trackingNumber) {
            $message .= " Tracking: {$trackingNumber}";
        }

        return $this->send($user->phone, $message, 'order_shipped', $user);
    }

    /**
     * Send order delivered SMS
     */
    public function sendOrderDelivered(User $user, string $orderNumber): ?SmsLog
    {
        if (!$user->phone || !Setting::get('sms_order_delivered', true)) {
            return null;
        }

        $message = "Your order #{$orderNumber} has been delivered! Enjoy your purchase.";

        return $this->send($user->phone, $message, 'order_delivered', $user);
    }

    /**
     * Send verification code SMS
     */
    public function sendVerificationCode(string $phone, string $code): SmsLog
    {
        $message = "Your verification code is: {$code}. Valid for 10 minutes.";

        return $this->send($phone, $message, 'verification');
    }

    /**
     * Send video call reminder SMS
     */
    public function sendVideoCallReminder(User $user, string $scheduledTime, string $joinUrl): ?SmsLog
    {
        if (!$user->phone || !Setting::get('sms_video_call_reminder', true)) {
            return null;
        }

        $message = "Reminder: You have a video call scheduled at {$scheduledTime}. Join here: {$joinUrl}";

        return $this->send($user->phone, $message, 'video_call_reminder', $user);
    }

    /**
     * Check if SMS is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('sms_enabled', false);
    }

    /**
     * Get current provider
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
