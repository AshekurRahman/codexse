<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class StripeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Payment Settings';
    protected static ?string $title = 'Payment Gateway Settings';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.admin.pages.stripe-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Stripe settings
            'stripe_key' => Setting::get('stripe_key', ''),
            'stripe_secret' => Setting::get('stripe_secret', ''),
            'stripe_webhook_secret' => Setting::get('stripe_webhook_secret', ''),
            'stripe_currency' => Setting::get('stripe_currency', 'usd'),
            'stripe_mode' => Setting::get('stripe_mode', 'test'),
            'stripe_enabled' => Setting::get('stripe_enabled', true),
            // PayPal settings
            'paypal_enabled' => Setting::get('paypal_enabled', false),
            'paypal_client_id' => Setting::get('paypal_client_id', ''),
            'paypal_secret' => Setting::get('paypal_secret', ''),
            'paypal_mode' => Setting::get('paypal_mode', 'sandbox'),
            // Payoneer settings
            'payoneer_enabled' => Setting::get('payoneer_enabled', false),
            'payoneer_program_id' => Setting::get('payoneer_program_id', ''),
            'payoneer_api_username' => Setting::get('payoneer_api_username', ''),
            'payoneer_api_password' => Setting::get('payoneer_api_password', ''),
            'payoneer_mode' => Setting::get('payoneer_mode', 'sandbox'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Payment Gateways')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Stripe')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\Toggle::make('stripe_enabled')
                                    ->label('Enable Stripe')
                                    ->helperText('Accept credit card payments via Stripe')
                                    ->default(true),

                                Forms\Components\Select::make('stripe_mode')
                                    ->label('Mode')
                                    ->options([
                                        'test' => 'Test Mode',
                                        'live' => 'Live Mode',
                                    ])
                                    ->default('test')
                                    ->helperText('Use Test Mode for development and testing.')
                                    ->required(),

                                Forms\Components\TextInput::make('stripe_key')
                                    ->label('Publishable Key')
                                    ->placeholder('pk_test_... or pk_live_...')
                                    ->helperText('Your Stripe publishable key')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('stripe_secret')
                                    ->label('Secret Key')
                                    ->password()
                                    ->placeholder('sk_test_... or sk_live_...')
                                    ->helperText('Your Stripe secret key')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('stripe_webhook_secret')
                                    ->label('Webhook Signing Secret')
                                    ->password()
                                    ->placeholder('whsec_...')
                                    ->helperText('Your webhook signing secret')
                                    ->maxLength(255),

                                Forms\Components\Placeholder::make('stripe_webhook_url')
                                    ->label('Webhook Endpoint')
                                    ->content(fn () => url('/stripe/webhook')),
                            ]),

                        Forms\Components\Tabs\Tab::make('PayPal')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Toggle::make('paypal_enabled')
                                    ->label('Enable PayPal')
                                    ->helperText('Accept PayPal payments')
                                    ->default(false),

                                Forms\Components\Select::make('paypal_mode')
                                    ->label('Mode')
                                    ->options([
                                        'sandbox' => 'Sandbox (Testing)',
                                        'live' => 'Live (Production)',
                                    ])
                                    ->default('sandbox'),

                                Forms\Components\TextInput::make('paypal_client_id')
                                    ->label('Client ID')
                                    ->placeholder('Your PayPal Client ID')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('paypal_secret')
                                    ->label('Client Secret')
                                    ->password()
                                    ->placeholder('Your PayPal Client Secret')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Tabs\Tab::make('Payoneer')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Toggle::make('payoneer_enabled')
                                    ->label('Enable Payoneer')
                                    ->helperText('Accept payments via Payoneer Checkout')
                                    ->default(false),

                                Forms\Components\Select::make('payoneer_mode')
                                    ->label('Mode')
                                    ->options([
                                        'sandbox' => 'Sandbox (Testing)',
                                        'live' => 'Live (Production)',
                                    ])
                                    ->default('sandbox')
                                    ->helperText('Use Sandbox for testing before going live'),

                                Forms\Components\TextInput::make('payoneer_program_id')
                                    ->label('Program ID')
                                    ->placeholder('Your Payoneer Program ID')
                                    ->helperText('Found in your Payoneer business account')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('payoneer_api_username')
                                    ->label('API Username')
                                    ->placeholder('Your API Username')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('payoneer_api_password')
                                    ->label('API Password')
                                    ->password()
                                    ->placeholder('Your API Password')
                                    ->maxLength(255),

                                Forms\Components\Placeholder::make('payoneer_info')
                                    ->label('Integration Info')
                                    ->content('Payoneer Checkout allows customers to pay using local payment methods, credit cards, and bank transfers worldwide.'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\Select::make('stripe_currency')
                            ->label('Default Currency')
                            ->options([
                                'usd' => 'USD - US Dollar',
                                'eur' => 'EUR - Euro',
                                'gbp' => 'GBP - British Pound',
                                'cad' => 'CAD - Canadian Dollar',
                                'aud' => 'AUD - Australian Dollar',
                                'jpy' => 'JPY - Japanese Yen',
                                'inr' => 'INR - Indian Rupee',
                                'bdt' => 'BDT - Bangladeshi Taka',
                            ])
                            ->default('usd')
                            ->helperText('Select the default currency for payments')
                            ->required(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Stripe settings
        Setting::set('stripe_enabled', $data['stripe_enabled'] ?? true, 'payment', 'boolean', false);
        Setting::set('stripe_key', $data['stripe_key'] ?? '', 'payment', 'string', false);
        Setting::set('stripe_secret', $data['stripe_secret'] ?? '', 'payment', 'string', true);
        Setting::set('stripe_webhook_secret', $data['stripe_webhook_secret'] ?? '', 'payment', 'string', true);
        Setting::set('stripe_currency', $data['stripe_currency'] ?? 'usd', 'payment', 'string', false);
        Setting::set('stripe_mode', $data['stripe_mode'] ?? 'test', 'payment', 'string', false);

        // Save PayPal settings
        Setting::set('paypal_enabled', $data['paypal_enabled'] ?? false, 'payment', 'boolean', false);
        Setting::set('paypal_client_id', $data['paypal_client_id'] ?? '', 'payment', 'string', false);
        Setting::set('paypal_secret', $data['paypal_secret'] ?? '', 'payment', 'string', true);
        Setting::set('paypal_mode', $data['paypal_mode'] ?? 'sandbox', 'payment', 'string', false);

        // Save Payoneer settings
        Setting::set('payoneer_enabled', $data['payoneer_enabled'] ?? false, 'payment', 'boolean', false);
        Setting::set('payoneer_program_id', $data['payoneer_program_id'] ?? '', 'payment', 'string', false);
        Setting::set('payoneer_api_username', $data['payoneer_api_username'] ?? '', 'payment', 'string', false);
        Setting::set('payoneer_api_password', $data['payoneer_api_password'] ?? '', 'payment', 'string', true);
        Setting::set('payoneer_mode', $data['payoneer_mode'] ?? 'sandbox', 'payment', 'string', false);

        Notification::make()
            ->title('Payment settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
