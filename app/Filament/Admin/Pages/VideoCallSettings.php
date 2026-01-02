<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Models\VideoCall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class VideoCallSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Video Calls';
    protected static ?string $title = 'Video Call Settings';
    protected static ?int $navigationSort = 115;

    protected static string $view = 'filament.admin.pages.video-call-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'video_calls_enabled' => Setting::get('video_calls_enabled', false),
            'video_call_provider' => Setting::get('video_call_provider', 'jitsi'),

            // Jitsi Settings
            'jitsi_domain' => Setting::get('jitsi_domain', 'meet.jit.si'),
            'jitsi_app_id' => Setting::get('jitsi_app_id', ''),
            'jitsi_secret' => Setting::get('jitsi_secret', ''),

            // Agora Settings
            'agora_app_id' => Setting::get('agora_app_id', ''),
            'agora_app_certificate' => Setting::get('agora_app_certificate', ''),

            // Twilio Settings
            'twilio_account_sid' => Setting::get('twilio_account_sid', ''),
            'twilio_api_key' => Setting::get('twilio_api_key', ''),
            'twilio_api_secret' => Setting::get('twilio_api_secret', ''),

            // Daily Settings
            'daily_api_key' => Setting::get('daily_api_key', ''),
            'daily_domain' => Setting::get('daily_domain', ''),

            // General Settings
            'video_call_max_duration' => Setting::get('video_call_max_duration', 60),
            'video_call_recording_enabled' => Setting::get('video_call_recording_enabled', false),
            'video_call_notify_before' => Setting::get('video_call_notify_before', 15),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Enable Video Calls')
                    ->schema([
                        Forms\Components\Toggle::make('video_calls_enabled')
                            ->label('Enable Video Call Feature')
                            ->helperText('Allow users to schedule and join video calls for service consultations')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('video_call_provider')
                            ->label('Video Call Provider')
                            ->options([
                                'jitsi' => 'Jitsi Meet (Free, Self-hosted option)',
                                'agora' => 'Agora (Enterprise)',
                                'twilio' => 'Twilio Video (Pay-as-you-go)',
                                'daily' => 'Daily.co (Developer-friendly)',
                            ])
                            ->default('jitsi')
                            ->helperText('Select the video calling service to use')
                            ->live(),
                    ]),

                Forms\Components\Section::make('Jitsi Meet Settings')
                    ->description('Free and open-source video conferencing')
                    ->schema([
                        Forms\Components\TextInput::make('jitsi_domain')
                            ->label('Jitsi Domain')
                            ->default('meet.jit.si')
                            ->placeholder('meet.jit.si or your self-hosted domain')
                            ->helperText('Use meet.jit.si for free public server or your own domain'),

                        Forms\Components\TextInput::make('jitsi_app_id')
                            ->label('App ID (Optional)')
                            ->placeholder('Your Jitsi App ID')
                            ->helperText('Required for JWT authentication on self-hosted'),

                        Forms\Components\TextInput::make('jitsi_secret')
                            ->label('Secret Key (Optional)')
                            ->password()
                            ->placeholder('Your Jitsi Secret')
                            ->helperText('Required for JWT authentication'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('video_call_provider') === 'jitsi'),

                Forms\Components\Section::make('Agora Settings')
                    ->description('Enterprise-grade video calling')
                    ->schema([
                        Forms\Components\TextInput::make('agora_app_id')
                            ->label('App ID')
                            ->placeholder('Your Agora App ID')
                            ->helperText('Found in Agora Console'),

                        Forms\Components\TextInput::make('agora_app_certificate')
                            ->label('App Certificate')
                            ->password()
                            ->placeholder('Your Agora App Certificate')
                            ->helperText('Required for token generation'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('video_call_provider') === 'agora'),

                Forms\Components\Section::make('Twilio Video Settings')
                    ->description('Programmable video with pay-as-you-go pricing')
                    ->schema([
                        Forms\Components\TextInput::make('twilio_account_sid')
                            ->label('Account SID')
                            ->placeholder('Your Twilio Account SID'),

                        Forms\Components\TextInput::make('twilio_api_key')
                            ->label('API Key SID')
                            ->placeholder('Your Twilio API Key'),

                        Forms\Components\TextInput::make('twilio_api_secret')
                            ->label('API Key Secret')
                            ->password()
                            ->placeholder('Your Twilio API Secret'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('video_call_provider') === 'twilio'),

                Forms\Components\Section::make('Daily.co Settings')
                    ->description('Developer-friendly video API')
                    ->schema([
                        Forms\Components\TextInput::make('daily_api_key')
                            ->label('API Key')
                            ->password()
                            ->placeholder('Your Daily.co API Key'),

                        Forms\Components\TextInput::make('daily_domain')
                            ->label('Domain')
                            ->placeholder('your-team.daily.co')
                            ->helperText('Your Daily.co subdomain'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('video_call_provider') === 'daily'),

                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\TextInput::make('video_call_max_duration')
                            ->label('Max Call Duration (minutes)')
                            ->numeric()
                            ->default(60)
                            ->minValue(15)
                            ->maxValue(240)
                            ->suffix('minutes'),

                        Forms\Components\TextInput::make('video_call_notify_before')
                            ->label('Reminder Notification')
                            ->numeric()
                            ->default(15)
                            ->suffix('minutes before call')
                            ->helperText('Send reminder this many minutes before scheduled call'),

                        Forms\Components\Toggle::make('video_call_recording_enabled')
                            ->label('Enable Call Recording')
                            ->helperText('Allow recording of video calls (where supported)'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Main settings
        Setting::set('video_calls_enabled', $data['video_calls_enabled'] ?? false, 'video', 'boolean', false);
        Setting::set('video_call_provider', $data['video_call_provider'] ?? 'jitsi', 'video', 'string', false);

        // Jitsi
        Setting::set('jitsi_domain', $data['jitsi_domain'] ?? 'meet.jit.si', 'video', 'string', false);
        Setting::set('jitsi_app_id', $data['jitsi_app_id'] ?? '', 'video', 'string', false);
        Setting::set('jitsi_secret', $data['jitsi_secret'] ?? '', 'video', 'string', true);

        // Agora
        Setting::set('agora_app_id', $data['agora_app_id'] ?? '', 'video', 'string', false);
        Setting::set('agora_app_certificate', $data['agora_app_certificate'] ?? '', 'video', 'string', true);

        // Twilio
        Setting::set('twilio_account_sid', $data['twilio_account_sid'] ?? '', 'video', 'string', false);
        Setting::set('twilio_api_key', $data['twilio_api_key'] ?? '', 'video', 'string', false);
        Setting::set('twilio_api_secret', $data['twilio_api_secret'] ?? '', 'video', 'string', true);

        // Daily
        Setting::set('daily_api_key', $data['daily_api_key'] ?? '', 'video', 'string', true);
        Setting::set('daily_domain', $data['daily_domain'] ?? '', 'video', 'string', false);

        // General
        Setting::set('video_call_max_duration', $data['video_call_max_duration'] ?? 60, 'video', 'integer', false);
        Setting::set('video_call_recording_enabled', $data['video_call_recording_enabled'] ?? false, 'video', 'boolean', false);
        Setting::set('video_call_notify_before', $data['video_call_notify_before'] ?? 15, 'video', 'integer', false);

        Notification::make()
            ->title('Video call settings saved')
            ->success()
            ->send();
    }

    public static function getNavigationBadge(): ?string
    {
        $active = VideoCall::active()->count();
        return $active > 0 ? (string) $active : null;
    }
}
