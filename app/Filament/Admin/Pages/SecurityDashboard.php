<?php

namespace App\Filament\Admin\Pages;

use App\Models\BlockedIp;
use App\Models\SecurityAlert;
use App\Models\SecurityLog;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class SecurityDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Security';
    protected static ?string $title = 'Security Dashboard';
    protected static ?int $navigationSort = 125;

    protected static string $view = 'filament.admin.pages.security-dashboard';

    public ?array $data = [];

    public ?string $blockIpAddress = null;
    public ?string $blockReason = null;
    public ?int $blockDuration = 24;

    public function mount(): void
    {
        $this->form->fill([
            'security_headers_enabled' => Setting::get('security_headers_enabled', true),
            'hsts_max_age' => Setting::get('hsts_max_age', 31536000),
            'csp_report_only' => Setting::get('csp_report_only', false),
            'ip_blocking_enabled' => Setting::get('ip_blocking_enabled', true),
            'auto_block_threshold' => Setting::get('auto_block_threshold', 10),
            'auto_block_duration' => Setting::get('auto_block_duration', 24),
            'honeypot_enabled' => Setting::get('honeypot_enabled', true),
            'bot_block_threshold' => Setting::get('bot_block_threshold', 5),
            'input_sanitization_enabled' => Setting::get('input_sanitization_enabled', true),
            'password_min_length' => Setting::get('password_min_length', 12),
            'password_require_mixed_case' => Setting::get('password_require_mixed_case', true),
            'password_require_numbers' => Setting::get('password_require_numbers', true),
            'password_require_symbols' => Setting::get('password_require_symbols', true),
            'password_prevent_reuse' => Setting::get('password_prevent_reuse', 5),
            'session_encrypt' => Setting::get('session_encrypt', true),
            'force_https' => Setting::get('force_https', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Security Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Headers')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Forms\Components\Toggle::make('security_headers_enabled')
                                    ->label('Enable Security Headers')
                                    ->helperText('Add security headers to all responses'),

                                Forms\Components\Toggle::make('csp_report_only')
                                    ->label('CSP Report-Only Mode')
                                    ->helperText('Log CSP violations without blocking'),

                                Forms\Components\TextInput::make('hsts_max_age')
                                    ->label('HSTS Max Age (seconds)')
                                    ->numeric()
                                    ->default(31536000)
                                    ->helperText('31536000 = 1 year (recommended)'),

                                Forms\Components\Toggle::make('force_https')
                                    ->label('Force HTTPS')
                                    ->helperText('Redirect all HTTP requests to HTTPS'),
                            ]),

                        Forms\Components\Tabs\Tab::make('IP Blocking')
                            ->icon('heroicon-o-no-symbol')
                            ->schema([
                                Forms\Components\Toggle::make('ip_blocking_enabled')
                                    ->label('Enable IP Blocking')
                                    ->helperText('Block malicious IP addresses'),

                                Forms\Components\TextInput::make('auto_block_threshold')
                                    ->label('Auto-Block Threshold')
                                    ->numeric()
                                    ->default(10)
                                    ->helperText('Number of suspicious requests before auto-block'),

                                Forms\Components\TextInput::make('auto_block_duration')
                                    ->label('Auto-Block Duration (hours)')
                                    ->numeric()
                                    ->default(24),
                            ]),

                        Forms\Components\Tabs\Tab::make('Bot Protection')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Forms\Components\Toggle::make('honeypot_enabled')
                                    ->label('Enable Honeypot Protection')
                                    ->helperText('Add hidden fields to detect bots'),

                                Forms\Components\TextInput::make('bot_block_threshold')
                                    ->label('Bot Block Threshold')
                                    ->numeric()
                                    ->default(5)
                                    ->helperText('Number of bot detections before blocking'),

                                Forms\Components\Toggle::make('input_sanitization_enabled')
                                    ->label('Enable Input Sanitization')
                                    ->helperText('Detect SQL injection, XSS, and other attacks'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Password Policy')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Forms\Components\TextInput::make('password_min_length')
                                    ->label('Minimum Password Length')
                                    ->numeric()
                                    ->default(12)
                                    ->minValue(8)
                                    ->maxValue(128),

                                Forms\Components\Toggle::make('password_require_mixed_case')
                                    ->label('Require Mixed Case')
                                    ->helperText('Require uppercase and lowercase letters'),

                                Forms\Components\Toggle::make('password_require_numbers')
                                    ->label('Require Numbers')
                                    ->helperText('Require at least one number'),

                                Forms\Components\Toggle::make('password_require_symbols')
                                    ->label('Require Symbols')
                                    ->helperText('Require at least one special character'),

                                Forms\Components\TextInput::make('password_prevent_reuse')
                                    ->label('Prevent Password Reuse')
                                    ->numeric()
                                    ->default(5)
                                    ->helperText('Number of previous passwords to check'),

                                Forms\Components\Toggle::make('session_encrypt')
                                    ->label('Encrypt Sessions')
                                    ->helperText('Encrypt session data for added security'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save all settings
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'security', is_bool($value) ? 'boolean' : 'string', false);
        }

        Notification::make()
            ->title('Security settings saved')
            ->success()
            ->send();
    }

    public function blockIp(): void
    {
        if (empty($this->blockIpAddress)) {
            Notification::make()
                ->title('IP address is required')
                ->danger()
                ->send();
            return;
        }

        BlockedIp::block(
            $this->blockIpAddress,
            $this->blockReason ?? 'Manual block',
            'admin:' . auth()->id(),
            $this->blockDuration
        );

        Notification::make()
            ->title('IP blocked successfully')
            ->body("IP {$this->blockIpAddress} has been blocked")
            ->success()
            ->send();

        $this->blockIpAddress = null;
        $this->blockReason = null;
        $this->blockDuration = 24;
    }

    public function unblockIp(int $id): void
    {
        $blocked = BlockedIp::find($id);

        if ($blocked) {
            $blocked->update(['is_active' => false]);
            Cache::forget("blocked_ip:{$blocked->ip_address}");

            Notification::make()
                ->title('IP unblocked')
                ->success()
                ->send();
        }
    }

    public function resolveAlert(int $id): void
    {
        $alert = SecurityAlert::find($id);

        if ($alert) {
            $alert->resolve(auth()->id());

            Notification::make()
                ->title('Alert resolved')
                ->success()
                ->send();
        }
    }

    public function getSecurityStats(): array
    {
        return [
            'blocked_ips' => BlockedIp::active()->count(),
            'security_events_today' => SecurityLog::whereDate('created_at', today())->count(),
            'critical_alerts' => SecurityAlert::unresolved()->critical()->count(),
            'unresolved_alerts' => SecurityAlert::unresolved()->count(),
            'attacks_blocked_today' => SecurityLog::whereDate('created_at', today())
                ->where('event_type', 'attack_detected')
                ->count(),
            'total_attacks_blocked' => SecurityLog::where('event_type', 'attack_detected')->count(),
        ];
    }

    public function getBlockedIps(): \Illuminate\Database\Eloquent\Collection
    {
        return BlockedIp::active()
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    }

    public function getRecentAlerts(): \Illuminate\Database\Eloquent\Collection
    {
        return SecurityAlert::unresolved()
            ->orderByDesc('severity')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }

    public function getRecentSecurityLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return SecurityLog::with('user')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    }

    public function getEventTypeStats(): array
    {
        return SecurityLog::whereDate('created_at', '>=', now()->subDays(7))
            ->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->pluck('count', 'event_type')
            ->toArray();
    }
}
