<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TaxSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Tax Settings';

    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.admin.pages.tax-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tax_enabled' => Setting::get('tax_enabled', false),
            'tax_label' => Setting::get('tax_label', 'Sales Tax'),
            'tax_display_in_cart' => Setting::get('tax_display_in_cart', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tax Configuration')
                    ->description('Enable and configure sales tax for your store.')
                    ->schema([
                        Forms\Components\Toggle::make('tax_enabled')
                            ->label('Enable Tax Calculation')
                            ->helperText('When enabled, tax will be calculated based on the customer\'s US state.')
                            ->live()
                            ->default(false),

                        Forms\Components\TextInput::make('tax_label')
                            ->label('Tax Label')
                            ->helperText('The label shown to customers (e.g., "Sales Tax", "State Tax").')
                            ->default('Sales Tax')
                            ->maxLength(50)
                            ->visible(fn (Forms\Get $get) => $get('tax_enabled')),

                        Forms\Components\Toggle::make('tax_display_in_cart')
                            ->label('Show Tax Estimate in Cart')
                            ->helperText('Display estimated tax in the shopping cart based on saved state.')
                            ->default(true)
                            ->visible(fn (Forms\Get $get) => $get('tax_enabled')),
                    ]),

                Forms\Components\Section::make('Tax Rates Overview')
                    ->description('Summary of configured tax rates by US state.')
                    ->schema([
                        Forms\Components\Placeholder::make('tax_rates_info')
                            ->content(function () {
                                $totalRates = TaxRate::count();
                                $activeRates = TaxRate::where('is_active', true)->count();
                                $avgRate = TaxRate::where('is_active', true)->avg('rate');

                                return "Total Rates: {$totalRates} | Active: {$activeRates} | Average Rate: " . number_format($avgRate ?? 0, 2) . '%';
                            }),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('manage_rates')
                                ->label('Manage Tax Rates')
                                ->icon('heroicon-o-cog-6-tooth')
                                ->url(fn () => route('filament.admin.resources.tax-rates.index'))
                                ->openUrlInNewTab(false),
                        ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('tax_enabled')),

                Forms\Components\Section::make('How Tax Works')
                    ->description('Understanding the tax calculation process.')
                    ->schema([
                        Forms\Components\Placeholder::make('how_it_works')
                            ->content('
                                1. Customer selects their US state at checkout
                                2. System looks up the tax rate for that state
                                3. Tax is calculated on the discounted subtotal: (Subtotal - Discount) × Rate
                                4. States without configured rates or no-tax states (AK, DE, MT, NH, OR) pay 0% tax
                            ')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('tax_enabled', $data['tax_enabled'] ?? false, 'tax', 'boolean');
        Setting::set('tax_label', $data['tax_label'] ?? 'Sales Tax', 'tax', 'string');
        Setting::set('tax_display_in_cart', $data['tax_display_in_cart'] ?? true, 'tax', 'boolean');

        Notification::make()
            ->title('Tax settings saved successfully')
            ->success()
            ->send();
    }

    public static function getNavigationBadge(): ?string
    {
        return Setting::get('tax_enabled', false) ? 'ON' : 'OFF';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Setting::get('tax_enabled', false) ? 'success' : 'gray';
    }
}
