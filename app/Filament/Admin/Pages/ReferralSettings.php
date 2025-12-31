<?php

namespace App\Filament\Admin\Pages;

use App\Models\ReferralSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReferralSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Referral Settings';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.admin.pages.referral-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'referral_program_enabled' => ReferralSetting::get('referral_program_enabled', '1') === '1',
            'signup_reward_referrer' => ReferralSetting::get('signup_reward_referrer', '5.00'),
            'signup_reward_referred' => ReferralSetting::get('signup_reward_referred', '5.00'),
            'purchase_commission_percent' => ReferralSetting::get('purchase_commission_percent', '10'),
            'min_withdrawal_amount' => ReferralSetting::get('min_withdrawal_amount', '20.00'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Program Status')
                    ->schema([
                        Forms\Components\Toggle::make('referral_program_enabled')
                            ->label('Enable Referral Program')
                            ->helperText('Turn the referral program on or off for all users.')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Signup Rewards')
                    ->description('Rewards given when a referred user signs up.')
                    ->schema([
                        Forms\Components\TextInput::make('signup_reward_referrer')
                            ->label('Reward for Referrer')
                            ->helperText('Amount the referrer receives when someone signs up using their link.')
                            ->numeric()
                            ->prefix('$')
                            ->default('5.00')
                            ->required(),
                        Forms\Components\TextInput::make('signup_reward_referred')
                            ->label('Welcome Bonus for New User')
                            ->helperText('Amount the new user receives as a welcome bonus.')
                            ->numeric()
                            ->prefix('$')
                            ->default('5.00')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Purchase Commission')
                    ->description('Commission earned when referred users make purchases.')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_commission_percent')
                            ->label('Commission Rate')
                            ->helperText('Percentage of purchase amount the referrer earns.')
                            ->numeric()
                            ->suffix('%')
                            ->default('10')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Withdrawal Settings')
                    ->schema([
                        Forms\Components\TextInput::make('min_withdrawal_amount')
                            ->label('Minimum Withdrawal Amount')
                            ->helperText('Minimum balance required to request a withdrawal.')
                            ->numeric()
                            ->prefix('$')
                            ->default('20.00')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        ReferralSetting::set('referral_program_enabled', $data['referral_program_enabled'] ? '1' : '0');
        ReferralSetting::set('signup_reward_referrer', $data['signup_reward_referrer']);
        ReferralSetting::set('signup_reward_referred', $data['signup_reward_referred']);
        ReferralSetting::set('purchase_commission_percent', $data['purchase_commission_percent']);
        ReferralSetting::set('min_withdrawal_amount', $data['min_withdrawal_amount']);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
