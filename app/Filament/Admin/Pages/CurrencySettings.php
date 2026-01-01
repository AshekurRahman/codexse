<?php

namespace App\Filament\Admin\Pages;

use App\Models\Currency;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CurrencySettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Currency Settings';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.admin.pages.currency-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'currency_mode' => Setting::get('currency_mode', 'multi'),
            'default_currency' => Setting::get('default_currency', 'USD'),
            'show_currency_selector' => Setting::get('show_currency_selector', true),
            'auto_detect_currency' => Setting::get('auto_detect_currency', false),
            'exchange_rate_api_key' => Setting::get('exchange_rate_api_key', ''),
            'auto_update_rates' => Setting::get('auto_update_rates', false),
            'rate_update_frequency' => Setting::get('rate_update_frequency', 'daily'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Currency Mode')
                    ->description('Choose whether to use a single currency or allow multiple currencies.')
                    ->schema([
                        Forms\Components\Radio::make('currency_mode')
                            ->label('Currency Mode')
                            ->options([
                                'single' => 'Single Currency - Display all prices in one currency only',
                                'multi' => 'Multi-Currency - Allow users to switch between currencies',
                            ])
                            ->default('multi')
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('default_currency')
                            ->label('Default Currency')
                            ->options(Currency::pluck('name', 'code')->mapWithKeys(fn ($name, $code) => [
                                $code => $code . ' - ' . $name
                            ]))
                            ->searchable()
                            ->default('USD')
                            ->required()
                            ->helperText('The base currency for all prices. In multi-currency mode, this is the default for new visitors.'),
                    ]),

                Forms\Components\Section::make('Multi-Currency Options')
                    ->description('Configure options for multi-currency mode.')
                    ->schema([
                        Forms\Components\Toggle::make('show_currency_selector')
                            ->label('Show Currency Selector')
                            ->helperText('Display the currency switcher in the navbar.')
                            ->default(true),

                        Forms\Components\Toggle::make('auto_detect_currency')
                            ->label('Auto-Detect Currency')
                            ->helperText('Automatically detect user\'s currency based on their location (requires GeoIP).')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('currency_mode') === 'multi'),

                Forms\Components\Section::make('Exchange Rate Updates')
                    ->description('Configure automatic exchange rate updates.')
                    ->schema([
                        Forms\Components\TextInput::make('exchange_rate_api_key')
                            ->label('Exchange Rate API Key')
                            ->helperText('API key from exchangerate-api.com for automatic rate updates.')
                            ->password()
                            ->revealable()
                            ->placeholder('Enter your API key'),

                        Forms\Components\Toggle::make('auto_update_rates')
                            ->label('Auto-Update Exchange Rates')
                            ->helperText('Automatically update exchange rates on a schedule.')
                            ->default(false)
                            ->live(),

                        Forms\Components\Select::make('rate_update_frequency')
                            ->label('Update Frequency')
                            ->options([
                                'hourly' => 'Every Hour',
                                'daily' => 'Once Daily',
                                'weekly' => 'Once Weekly',
                            ])
                            ->default('daily')
                            ->visible(fn (Forms\Get $get) => $get('auto_update_rates')),
                    ])
                    ->columns(1)
                    ->visible(fn (Forms\Get $get) => $get('currency_mode') === 'multi'),

                Forms\Components\Section::make('Active Currencies')
                    ->description('Quick overview of active currencies.')
                    ->schema([
                        Forms\Components\Placeholder::make('active_currencies')
                            ->label('')
                            ->content(function () {
                                $currencies = Currency::where('is_active', true)->get();
                                $list = $currencies->map(fn ($c) => "{$c->symbol} {$c->code}")->join(', ');
                                return "Active: {$currencies->count()} currencies ({$list})";
                            }),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('manage_currencies')
                                ->label('Manage Currencies')
                                ->url(fn () => route('filament.admin.resources.currencies.index'))
                                ->icon('heroicon-o-cog-6-tooth')
                                ->color('gray'),
                        ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('currency_mode') === 'multi'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('currency_mode', $data['currency_mode'], 'currency', 'string');
        Setting::set('default_currency', $data['default_currency'], 'currency', 'string');
        Setting::set('show_currency_selector', $data['show_currency_selector'] ?? true, 'currency', 'boolean');
        Setting::set('auto_detect_currency', $data['auto_detect_currency'] ?? false, 'currency', 'boolean');
        Setting::set('exchange_rate_api_key', $data['exchange_rate_api_key'] ?? '', 'currency', 'string');
        Setting::set('auto_update_rates', $data['auto_update_rates'] ?? false, 'currency', 'boolean');
        Setting::set('rate_update_frequency', $data['rate_update_frequency'] ?? 'daily', 'currency', 'string');

        // Update default currency in Currency model
        if ($data['default_currency']) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            Currency::where('code', $data['default_currency'])->update(['is_default' => true]);
        }

        // Clear currency cache
        Currency::clearCache();

        Notification::make()
            ->title('Currency settings saved successfully')
            ->success()
            ->send();
    }

    public static function isSingleCurrencyMode(): bool
    {
        return Setting::get('currency_mode', 'multi') === 'single';
    }

    public static function isMultiCurrencyMode(): bool
    {
        return Setting::get('currency_mode', 'multi') === 'multi';
    }

    public static function shouldShowCurrencySelector(): bool
    {
        return self::isMultiCurrencyMode() && Setting::get('show_currency_selector', true);
    }
}
