<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Models\SmsLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SmsSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'SMS Notifications';
    protected static ?string $title = 'SMS Notification Settings';
    protected static ?int $navigationSort = 118;

    protected static string $view = 'filament.admin.pages.sms-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'sms_enabled' => Setting::get('sms_enabled', false),
            'sms_provider' => Setting::get('sms_provider', 'twilio'),

            // Twilio
            'twilio_sms_account_sid' => Setting::get('twilio_sms_account_sid', ''),
            'twilio_sms_auth_token' => Setting::get('twilio_sms_auth_token', ''),
            'twilio_sms_from_number' => Setting::get('twilio_sms_from_number', ''),

            // Nexmo/Vonage
            'nexmo_api_key' => Setting::get('nexmo_api_key', ''),
            'nexmo_api_secret' => Setting::get('nexmo_api_secret', ''),
            'nexmo_from_number' => Setting::get('nexmo_from_number', ''),

            // AWS SNS
            'aws_sns_access_key' => Setting::get('aws_sns_access_key', ''),
            'aws_sns_secret_key' => Setting::get('aws_sns_secret_key', ''),
            'aws_sns_region' => Setting::get('aws_sns_region', 'us-east-1'),

            // Notification Types
            'sms_order_confirmation' => Setting::get('sms_order_confirmation', true),
            'sms_order_shipped' => Setting::get('sms_order_shipped', true),
            'sms_order_delivered' => Setting::get('sms_order_delivered', true),
            'sms_order_cancelled' => Setting::get('sms_order_cancelled', true),
            'sms_payout_completed' => Setting::get('sms_payout_completed', true),
            'sms_video_call_reminder' => Setting::get('sms_video_call_reminder', true),
            'sms_service_updates' => Setting::get('sms_service_updates', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SMS Service')
                    ->schema([
                        Forms\Components\Toggle::make('sms_enabled')
                            ->label('Enable SMS Notifications')
                            ->helperText('Send SMS notifications to users for important updates')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('sms_provider')
                            ->label('SMS Provider')
                            ->options([
                                'twilio' => 'Twilio',
                                'nexmo' => 'Vonage (Nexmo)',
                                'sns' => 'AWS SNS',
                            ])
                            ->default('twilio')
                            ->live(),
                    ]),

                Forms\Components\Section::make('Twilio Settings')
                    ->schema([
                        Forms\Components\TextInput::make('twilio_sms_account_sid')
                            ->label('Account SID')
                            ->placeholder('ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->helperText('Found in Twilio Console'),

                        Forms\Components\TextInput::make('twilio_sms_auth_token')
                            ->label('Auth Token')
                            ->password()
                            ->placeholder('Your Auth Token'),

                        Forms\Components\TextInput::make('twilio_sms_from_number')
                            ->label('From Phone Number')
                            ->placeholder('+1234567890')
                            ->helperText('Your Twilio phone number'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('sms_provider') === 'twilio'),

                Forms\Components\Section::make('Vonage (Nexmo) Settings')
                    ->schema([
                        Forms\Components\TextInput::make('nexmo_api_key')
                            ->label('API Key')
                            ->placeholder('Your API Key'),

                        Forms\Components\TextInput::make('nexmo_api_secret')
                            ->label('API Secret')
                            ->password()
                            ->placeholder('Your API Secret'),

                        Forms\Components\TextInput::make('nexmo_from_number')
                            ->label('From Number/Name')
                            ->placeholder('+1234567890 or BRAND')
                            ->helperText('Phone number or alphanumeric sender ID'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('sms_provider') === 'nexmo'),

                Forms\Components\Section::make('AWS SNS Settings')
                    ->schema([
                        Forms\Components\TextInput::make('aws_sns_access_key')
                            ->label('Access Key ID')
                            ->placeholder('AKIAIOSFODNN7EXAMPLE'),

                        Forms\Components\TextInput::make('aws_sns_secret_key')
                            ->label('Secret Access Key')
                            ->password()
                            ->placeholder('wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'),

                        Forms\Components\Select::make('aws_sns_region')
                            ->label('Region')
                            ->options([
                                'us-east-1' => 'US East (N. Virginia)',
                                'us-west-2' => 'US West (Oregon)',
                                'eu-west-1' => 'Europe (Ireland)',
                                'ap-southeast-1' => 'Asia Pacific (Singapore)',
                            ])
                            ->default('us-east-1'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('sms_provider') === 'sns'),

                Forms\Components\Section::make('Notification Types')
                    ->description('Select which events trigger SMS notifications')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('sms_order_confirmation')
                                    ->label('Order Confirmation')
                                    ->helperText('When order is placed'),

                                Forms\Components\Toggle::make('sms_order_shipped')
                                    ->label('Order Shipped')
                                    ->helperText('When order ships'),

                                Forms\Components\Toggle::make('sms_order_delivered')
                                    ->label('Order Delivered')
                                    ->helperText('When order is delivered'),

                                Forms\Components\Toggle::make('sms_order_cancelled')
                                    ->label('Order Cancelled')
                                    ->helperText('When order is cancelled'),

                                Forms\Components\Toggle::make('sms_payout_completed')
                                    ->label('Payout Completed')
                                    ->helperText('When seller payout is sent'),

                                Forms\Components\Toggle::make('sms_video_call_reminder')
                                    ->label('Video Call Reminder')
                                    ->helperText('Before scheduled calls'),

                                Forms\Components\Toggle::make('sms_service_updates')
                                    ->label('Service Order Updates')
                                    ->helperText('Service order status changes'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Main settings
        Setting::set('sms_enabled', $data['sms_enabled'] ?? false, 'sms', 'boolean', false);
        Setting::set('sms_provider', $data['sms_provider'] ?? 'twilio', 'sms', 'string', false);

        // Twilio
        Setting::set('twilio_sms_account_sid', $data['twilio_sms_account_sid'] ?? '', 'sms', 'string', false);
        Setting::set('twilio_sms_auth_token', $data['twilio_sms_auth_token'] ?? '', 'sms', 'string', true);
        Setting::set('twilio_sms_from_number', $data['twilio_sms_from_number'] ?? '', 'sms', 'string', false);

        // Nexmo
        Setting::set('nexmo_api_key', $data['nexmo_api_key'] ?? '', 'sms', 'string', false);
        Setting::set('nexmo_api_secret', $data['nexmo_api_secret'] ?? '', 'sms', 'string', true);
        Setting::set('nexmo_from_number', $data['nexmo_from_number'] ?? '', 'sms', 'string', false);

        // AWS SNS
        Setting::set('aws_sns_access_key', $data['aws_sns_access_key'] ?? '', 'sms', 'string', false);
        Setting::set('aws_sns_secret_key', $data['aws_sns_secret_key'] ?? '', 'sms', 'string', true);
        Setting::set('aws_sns_region', $data['aws_sns_region'] ?? 'us-east-1', 'sms', 'string', false);

        // Notification types
        Setting::set('sms_order_confirmation', $data['sms_order_confirmation'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_order_shipped', $data['sms_order_shipped'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_order_delivered', $data['sms_order_delivered'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_order_cancelled', $data['sms_order_cancelled'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_payout_completed', $data['sms_payout_completed'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_video_call_reminder', $data['sms_video_call_reminder'] ?? true, 'sms', 'boolean', false);
        Setting::set('sms_service_updates', $data['sms_service_updates'] ?? true, 'sms', 'boolean', false);

        Notification::make()
            ->title('SMS settings saved')
            ->success()
            ->send();
    }

    public function testSms(): void
    {
        // This would send a test SMS
        Notification::make()
            ->title('Test SMS sent')
            ->body('Check your phone for the test message')
            ->success()
            ->send();
    }

    public function getSmsStats(): array
    {
        return [
            'total' => SmsLog::count(),
            'sent' => SmsLog::where('status', 'sent')->count(),
            'delivered' => SmsLog::where('status', 'delivered')->count(),
            'failed' => SmsLog::where('status', 'failed')->count(),
            'cost' => SmsLog::sum('cost'),
        ];
    }
}
