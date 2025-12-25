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
    protected static ?string $title = 'Stripe Payment Settings';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.admin.pages.stripe-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'stripe_key' => Setting::get('stripe_key', ''),
            'stripe_secret' => Setting::get('stripe_secret', ''),
            'stripe_webhook_secret' => Setting::get('stripe_webhook_secret', ''),
            'stripe_currency' => Setting::get('stripe_currency', 'usd'),
            'stripe_mode' => Setting::get('stripe_mode', 'test'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Keys')
                    ->description('Enter your Stripe API keys. You can find these in your Stripe Dashboard under Developers > API keys.')
                    ->schema([
                        Forms\Components\Select::make('stripe_mode')
                            ->label('Mode')
                            ->options([
                                'test' => 'Test Mode',
                                'live' => 'Live Mode',
                            ])
                            ->default('test')
                            ->helperText('Use Test Mode for development and testing. Switch to Live Mode when ready to accept real payments.')
                            ->required(),

                        Forms\Components\TextInput::make('stripe_key')
                            ->label('Publishable Key')
                            ->placeholder('pk_test_... or pk_live_...')
                            ->helperText('Your Stripe publishable key (starts with pk_test_ or pk_live_)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_secret')
                            ->label('Secret Key')
                            ->password()
                            ->placeholder('sk_test_... or sk_live_...')
                            ->helperText('Your Stripe secret key (starts with sk_test_ or sk_live_). This is sensitive - keep it secure!')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_webhook_secret')
                            ->label('Webhook Signing Secret')
                            ->password()
                            ->placeholder('whsec_...')
                            ->helperText('Your webhook signing secret (starts with whsec_). Required for processing payment confirmations.')
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Payment Options')
                    ->schema([
                        Forms\Components\Select::make('stripe_currency')
                            ->label('Currency')
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

                Forms\Components\Section::make('Webhook URL')
                    ->description('Configure this URL in your Stripe Dashboard under Developers > Webhooks')
                    ->schema([
                        Forms\Components\Placeholder::make('webhook_url')
                            ->label('Your Webhook Endpoint')
                            ->content(fn () => url('/stripe/webhook')),

                        Forms\Components\Placeholder::make('webhook_events')
                            ->label('Required Events')
                            ->content('checkout.session.completed, payment_intent.succeeded, payment_intent.payment_failed'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save each setting (secrets are encrypted)
        Setting::set('stripe_key', $data['stripe_key'], 'stripe', 'string', false);
        Setting::set('stripe_secret', $data['stripe_secret'], 'stripe', 'string', true);
        Setting::set('stripe_webhook_secret', $data['stripe_webhook_secret'] ?? '', 'stripe', 'string', true);
        Setting::set('stripe_currency', $data['stripe_currency'], 'stripe', 'string', false);
        Setting::set('stripe_mode', $data['stripe_mode'], 'stripe', 'string', false);

        Notification::make()
            ->title('Settings saved successfully')
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
