<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CommissionSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Commission Settings';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.admin.pages.commission-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_commission_rate' => Setting::get('default_commission_rate', '20'),
            'new_seller_commission_rate' => Setting::get('new_seller_commission_rate', '25'),
            'established_seller_commission_rate' => Setting::get('established_seller_commission_rate', '20'),
            'top_seller_commission_rate' => Setting::get('top_seller_commission_rate', '15'),
            'use_seller_level_rates' => Setting::get('use_seller_level_rates', false),
            'service_commission_rate' => Setting::get('service_commission_rate', '20'),
            'job_commission_rate' => Setting::get('job_commission_rate', '10'),
            'min_payout_amount' => Setting::get('min_payout_amount', '50'),
            'payout_hold_days' => Setting::get('payout_hold_days', '14'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Default Commission Rate')
                    ->description('The standard platform commission rate applied to all sales.')
                    ->schema([
                        Forms\Components\TextInput::make('default_commission_rate')
                            ->label('Default Commission Rate')
                            ->helperText('Percentage of each sale that goes to the platform. For example, 20 means the platform takes 20% and the seller receives 80%.')
                            ->numeric()
                            ->suffix('%')
                            ->default('20')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Seller Level Commission Rates')
                    ->description('Different commission rates based on seller performance level.')
                    ->schema([
                        Forms\Components\Toggle::make('use_seller_level_rates')
                            ->label('Enable Seller Level Rates')
                            ->helperText('When enabled, commission rates will be based on seller level instead of the default rate.')
                            ->default(false)
                            ->live(),
                        Forms\Components\TextInput::make('new_seller_commission_rate')
                            ->label('New Seller Rate')
                            ->helperText('Rate for sellers who just joined (Level 1).')
                            ->numeric()
                            ->suffix('%')
                            ->default('25')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('use_seller_level_rates')),
                        Forms\Components\TextInput::make('established_seller_commission_rate')
                            ->label('Established Seller Rate')
                            ->helperText('Rate for established sellers (Level 2).')
                            ->numeric()
                            ->suffix('%')
                            ->default('20')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('use_seller_level_rates')),
                        Forms\Components\TextInput::make('top_seller_commission_rate')
                            ->label('Top Seller Rate')
                            ->helperText('Rate for top sellers (Level 3+). Lower rate rewards top performers.')
                            ->numeric()
                            ->suffix('%')
                            ->default('15')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('use_seller_level_rates')),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Service & Job Commission Rates')
                    ->description('Commission rates for services marketplace and job contracts.')
                    ->schema([
                        Forms\Components\TextInput::make('service_commission_rate')
                            ->label('Service Commission Rate')
                            ->helperText('Platform commission for freelance services.')
                            ->numeric()
                            ->suffix('%')
                            ->default('20')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\TextInput::make('job_commission_rate')
                            ->label('Job Contract Commission Rate')
                            ->helperText('Platform commission for job contracts.')
                            ->numeric()
                            ->suffix('%')
                            ->default('10')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payout Settings')
                    ->description('Configure when and how sellers can withdraw their earnings.')
                    ->schema([
                        Forms\Components\TextInput::make('min_payout_amount')
                            ->label('Minimum Payout Amount')
                            ->helperText('Minimum balance required for sellers to request a payout.')
                            ->numeric()
                            ->prefix('$')
                            ->default('50')
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('payout_hold_days')
                            ->label('Payout Hold Period')
                            ->helperText('Number of days earnings are held before becoming available for payout (for refund protection).')
                            ->numeric()
                            ->suffix('days')
                            ->default('14')
                            ->minValue(0)
                            ->maxValue(90)
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('default_commission_rate', $data['default_commission_rate'], 'commission', 'integer');
        Setting::set('new_seller_commission_rate', $data['new_seller_commission_rate'], 'commission', 'integer');
        Setting::set('established_seller_commission_rate', $data['established_seller_commission_rate'], 'commission', 'integer');
        Setting::set('top_seller_commission_rate', $data['top_seller_commission_rate'], 'commission', 'integer');
        Setting::set('use_seller_level_rates', $data['use_seller_level_rates'], 'commission', 'boolean');
        Setting::set('service_commission_rate', $data['service_commission_rate'], 'commission', 'integer');
        Setting::set('job_commission_rate', $data['job_commission_rate'], 'commission', 'integer');
        Setting::set('min_payout_amount', $data['min_payout_amount'], 'commission', 'integer');
        Setting::set('payout_hold_days', $data['payout_hold_days'], 'commission', 'integer');

        Notification::make()
            ->title('Commission settings saved successfully')
            ->success()
            ->send();
    }

    /**
     * Get commission rate for a seller based on current settings.
     */
    public static function getCommissionRateForSeller($seller): float
    {
        // If seller has a custom rate set, use it
        if ($seller->commission_rate !== null && $seller->commission_rate > 0) {
            return $seller->commission_rate / 100;
        }

        // Check if level-based rates are enabled
        if (Setting::get('use_seller_level_rates', false)) {
            $level = $seller->level ?? 1;

            // Handle both numeric and string levels
            $levelNum = match (true) {
                is_numeric($level) => (int) $level,
                $level === 'platinum' || $level === 'gold' => 3,
                $level === 'silver' => 2,
                default => 1,
            };

            return match (true) {
                $levelNum >= 3 => (float) Setting::get('top_seller_commission_rate', 15) / 100,
                $levelNum == 2 => (float) Setting::get('established_seller_commission_rate', 20) / 100,
                default => (float) Setting::get('new_seller_commission_rate', 25) / 100,
            };
        }

        // Use default rate
        return (float) Setting::get('default_commission_rate', 20) / 100;
    }

    /**
     * Get commission rate for services.
     */
    public static function getServiceCommissionRate(): float
    {
        return (float) Setting::get('service_commission_rate', 20) / 100;
    }

    /**
     * Get commission rate for job contracts.
     */
    public static function getJobCommissionRate(): float
    {
        return (float) Setting::get('job_commission_rate', 10) / 100;
    }
}
