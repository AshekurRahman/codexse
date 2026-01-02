<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitingSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Security';
    protected static ?string $navigationLabel = 'Rate Limiting';
    protected static ?string $title = 'API Rate Limiting';
    protected static ?int $navigationSort = 50;

    protected static string $view = 'filament.admin.pages.rate-limiting-settings';

    public ?array $data = [];
    public array $rateLimitStats = [];

    public function mount(): void
    {
        $this->loadRateLimitStats();

        $this->form->fill([
            // Global settings
            'rate_limiting_enabled' => Setting::get('rate_limiting_enabled', true),
            'global_rate_limit' => Setting::get('global_rate_limit', 60),
            'global_rate_decay' => Setting::get('global_rate_decay', 1),

            // API settings
            'api_rate_limit' => Setting::get('api_rate_limit', 100),
            'api_rate_decay' => Setting::get('api_rate_decay', 1),

            // Authentication settings
            'login_rate_limit' => Setting::get('login_rate_limit', 5),
            'login_rate_decay' => Setting::get('login_rate_decay', 1),
            'register_rate_limit' => Setting::get('register_rate_limit', 3),
            'register_rate_decay' => Setting::get('register_rate_decay', 1),

            // Search settings
            'search_rate_limit' => Setting::get('search_rate_limit', 30),
            'search_rate_decay' => Setting::get('search_rate_decay', 1),

            // Checkout settings
            'checkout_rate_limit' => Setting::get('checkout_rate_limit', 10),
            'checkout_rate_decay' => Setting::get('checkout_rate_decay', 1),

            // Contact/Message settings
            'contact_rate_limit' => Setting::get('contact_rate_limit', 5),
            'contact_rate_decay' => Setting::get('contact_rate_decay', 5),

            // Whitelisted IPs
            'rate_limit_whitelist' => Setting::get('rate_limit_whitelist', ''),

            // Custom response
            'rate_limit_message' => Setting::get('rate_limit_message', 'Too many requests. Please try again later.'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Global Settings')
                    ->description('Enable or disable rate limiting across the platform')
                    ->schema([
                        Forms\Components\Toggle::make('rate_limiting_enabled')
                            ->label('Enable Rate Limiting')
                            ->helperText('Master switch for all rate limiting features')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('global_rate_limit')
                                    ->label('Global Rate Limit')
                                    ->numeric()
                                    ->minValue(10)
                                    ->maxValue(1000)
                                    ->suffix('requests')
                                    ->helperText('Maximum requests per decay period'),

                                Forms\Components\TextInput::make('global_rate_decay')
                                    ->label('Decay Period')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60)
                                    ->suffix('minutes')
                                    ->helperText('Time window for rate limiting'),
                            ]),
                    ]),

                Forms\Components\Section::make('API Endpoints')
                    ->description('Rate limits for API requests')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('api_rate_limit')
                                    ->label('API Rate Limit')
                                    ->numeric()
                                    ->minValue(10)
                                    ->maxValue(1000)
                                    ->suffix('requests')
                                    ->helperText('Maximum API requests'),

                                Forms\Components\TextInput::make('api_rate_decay')
                                    ->label('Decay Period')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60)
                                    ->suffix('minutes'),
                            ]),
                    ]),

                Forms\Components\Section::make('Authentication')
                    ->description('Protect login and registration from brute force attacks')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('login_rate_limit')
                                    ->label('Login Attempts')
                                    ->numeric()
                                    ->minValue(3)
                                    ->maxValue(20)
                                    ->suffix('attempts'),

                                Forms\Components\TextInput::make('login_rate_decay')
                                    ->label('Login Decay')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60)
                                    ->suffix('minutes'),

                                Forms\Components\TextInput::make('register_rate_limit')
                                    ->label('Registration Attempts')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->suffix('attempts'),

                                Forms\Components\TextInput::make('register_rate_decay')
                                    ->label('Register Decay')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60)
                                    ->suffix('minutes'),
                            ]),
                    ]),

                Forms\Components\Section::make('Other Endpoints')
                    ->description('Rate limits for search, checkout, and contact forms')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Fieldset::make('Search')
                                    ->schema([
                                        Forms\Components\TextInput::make('search_rate_limit')
                                            ->label('Limit')
                                            ->numeric()
                                            ->suffix('requests'),
                                        Forms\Components\TextInput::make('search_rate_decay')
                                            ->label('Decay')
                                            ->numeric()
                                            ->suffix('min'),
                                    ]),

                                Forms\Components\Fieldset::make('Checkout')
                                    ->schema([
                                        Forms\Components\TextInput::make('checkout_rate_limit')
                                            ->label('Limit')
                                            ->numeric()
                                            ->suffix('requests'),
                                        Forms\Components\TextInput::make('checkout_rate_decay')
                                            ->label('Decay')
                                            ->numeric()
                                            ->suffix('min'),
                                    ]),

                                Forms\Components\Fieldset::make('Contact/Messages')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_rate_limit')
                                            ->label('Limit')
                                            ->numeric()
                                            ->suffix('requests'),
                                        Forms\Components\TextInput::make('contact_rate_decay')
                                            ->label('Decay')
                                            ->numeric()
                                            ->suffix('min'),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Whitelist & Response')
                    ->description('Configure IP whitelist and custom rate limit response')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('rate_limit_whitelist')
                            ->label('Whitelisted IP Addresses')
                            ->rows(3)
                            ->placeholder("192.168.1.1\n10.0.0.1")
                            ->helperText('One IP per line. These IPs bypass rate limiting.'),

                        Forms\Components\TextInput::make('rate_limit_message')
                            ->label('Rate Limit Message')
                            ->placeholder('Too many requests. Please try again later.')
                            ->helperText('Message shown when rate limit is exceeded'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Global settings
        Setting::set('rate_limiting_enabled', $data['rate_limiting_enabled'] ?? true, 'security', 'boolean', false);
        Setting::set('global_rate_limit', $data['global_rate_limit'] ?? 60, 'security', 'integer', false);
        Setting::set('global_rate_decay', $data['global_rate_decay'] ?? 1, 'security', 'integer', false);

        // API settings
        Setting::set('api_rate_limit', $data['api_rate_limit'] ?? 100, 'security', 'integer', false);
        Setting::set('api_rate_decay', $data['api_rate_decay'] ?? 1, 'security', 'integer', false);

        // Auth settings
        Setting::set('login_rate_limit', $data['login_rate_limit'] ?? 5, 'security', 'integer', false);
        Setting::set('login_rate_decay', $data['login_rate_decay'] ?? 1, 'security', 'integer', false);
        Setting::set('register_rate_limit', $data['register_rate_limit'] ?? 3, 'security', 'integer', false);
        Setting::set('register_rate_decay', $data['register_rate_decay'] ?? 1, 'security', 'integer', false);

        // Other endpoints
        Setting::set('search_rate_limit', $data['search_rate_limit'] ?? 30, 'security', 'integer', false);
        Setting::set('search_rate_decay', $data['search_rate_decay'] ?? 1, 'security', 'integer', false);
        Setting::set('checkout_rate_limit', $data['checkout_rate_limit'] ?? 10, 'security', 'integer', false);
        Setting::set('checkout_rate_decay', $data['checkout_rate_decay'] ?? 1, 'security', 'integer', false);
        Setting::set('contact_rate_limit', $data['contact_rate_limit'] ?? 5, 'security', 'integer', false);
        Setting::set('contact_rate_decay', $data['contact_rate_decay'] ?? 5, 'security', 'integer', false);

        // Whitelist and message
        Setting::set('rate_limit_whitelist', $data['rate_limit_whitelist'] ?? '', 'security', 'text', false);
        Setting::set('rate_limit_message', $data['rate_limit_message'] ?? 'Too many requests.', 'security', 'string', false);

        // Clear rate limiter cache to apply new settings
        Cache::forget('rate_limit_settings');

        Notification::make()
            ->title('Rate limiting settings saved')
            ->success()
            ->send();
    }

    public function loadRateLimitStats(): void
    {
        // This would normally fetch from cache/database
        // For now, we'll show placeholder stats
        $this->rateLimitStats = [
            'total_blocked' => Cache::get('rate_limit_blocked_count', 0),
            'blocked_today' => Cache::get('rate_limit_blocked_today', 0),
            'top_blocked_ips' => Cache::get('rate_limit_top_blocked', []),
        ];
    }

    public function clearRateLimitCache(): void
    {
        // Clear all rate limiter caches
        Cache::forget('rate_limit_blocked_count');
        Cache::forget('rate_limit_blocked_today');
        Cache::forget('rate_limit_top_blocked');

        Notification::make()
            ->title('Rate limit cache cleared')
            ->success()
            ->send();

        $this->loadRateLimitStats();
    }

    /**
     * Get rate limit settings for use in middleware
     */
    public static function getRateLimitSettings(): array
    {
        return Cache::remember('rate_limit_settings', 3600, function () {
            return [
                'enabled' => Setting::get('rate_limiting_enabled', true),
                'global' => [
                    'limit' => Setting::get('global_rate_limit', 60),
                    'decay' => Setting::get('global_rate_decay', 1),
                ],
                'api' => [
                    'limit' => Setting::get('api_rate_limit', 100),
                    'decay' => Setting::get('api_rate_decay', 1),
                ],
                'login' => [
                    'limit' => Setting::get('login_rate_limit', 5),
                    'decay' => Setting::get('login_rate_decay', 1),
                ],
                'register' => [
                    'limit' => Setting::get('register_rate_limit', 3),
                    'decay' => Setting::get('register_rate_decay', 1),
                ],
                'search' => [
                    'limit' => Setting::get('search_rate_limit', 30),
                    'decay' => Setting::get('search_rate_decay', 1),
                ],
                'checkout' => [
                    'limit' => Setting::get('checkout_rate_limit', 10),
                    'decay' => Setting::get('checkout_rate_decay', 1),
                ],
                'contact' => [
                    'limit' => Setting::get('contact_rate_limit', 5),
                    'decay' => Setting::get('contact_rate_decay', 5),
                ],
                'whitelist' => array_filter(explode("\n", Setting::get('rate_limit_whitelist', ''))),
                'message' => Setting::get('rate_limit_message', 'Too many requests. Please try again later.'),
            ];
        });
    }

    /**
     * Check if an IP is whitelisted
     */
    public static function isWhitelisted(string $ip): bool
    {
        $settings = static::getRateLimitSettings();
        return in_array($ip, $settings['whitelist']);
    }
}
