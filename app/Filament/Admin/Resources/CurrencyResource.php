<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use App\Services\CurrencyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Currencies';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Currency Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Currency Code')
                            ->placeholder('USD')
                            ->required()
                            ->maxLength(3)
                            ->unique(ignoreRecord: true)
                            ->helperText('3-letter ISO currency code'),

                        Forms\Components\TextInput::make('name')
                            ->label('Currency Name')
                            ->placeholder('US Dollar')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('symbol')
                            ->label('Symbol')
                            ->placeholder('$')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Select::make('symbol_position')
                            ->label('Symbol Position')
                            ->options([
                                'before' => 'Before amount ($100)',
                                'after' => 'After amount (100 €)',
                            ])
                            ->default('before')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Exchange Rate')
                    ->schema([
                        Forms\Components\TextInput::make('exchange_rate')
                            ->label('Exchange Rate')
                            ->numeric()
                            ->required()
                            ->default(1.000000)
                            ->step(0.000001)
                            ->helperText('Rate relative to base currency (USD). 1 USD = X of this currency.'),

                        Forms\Components\Placeholder::make('rate_updated_at')
                            ->label('Last Rate Update')
                            ->content(fn (?Currency $record): string => $record?->rate_updated_at?->diffForHumans() ?? 'Never'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Formatting')
                    ->schema([
                        Forms\Components\TextInput::make('decimal_separator')
                            ->label('Decimal Separator')
                            ->default('.')
                            ->maxLength(1)
                            ->required(),

                        Forms\Components\TextInput::make('thousand_separator')
                            ->label('Thousand Separator')
                            ->default(',')
                            ->maxLength(1)
                            ->required(),

                        Forms\Components\TextInput::make('decimal_places')
                            ->label('Decimal Places')
                            ->numeric()
                            ->default(2)
                            ->minValue(0)
                            ->maxValue(4)
                            ->required(),

                        Forms\Components\Placeholder::make('preview')
                            ->label('Format Preview')
                            ->content(function (?Currency $record): string {
                                if (!$record) {
                                    return '$1,234.56';
                                }
                                return $record->format(1234.56);
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive currencies are hidden from users'),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Currency')
                            ->default(false)
                            ->helperText('Base currency for all prices (usually USD)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('symbol')
                    ->label('Symbol')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric(6)
                    ->sortable(),

                Tables\Columns\TextColumn::make('format_preview')
                    ->label('Format Preview')
                    ->state(fn (Currency $record): string => $record->format(1234.56)),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),

                Tables\Columns\TextColumn::make('rate_updated_at')
                    ->label('Rate Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_default')
                    ->label('Set as Default')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Currency $record) {
                        $record->setAsDefault();
                        Notification::make()
                            ->title('Default currency updated')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Currency $record) => !$record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('update_rates')
                    ->label('Update Exchange Rates')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {
                        $service = app(CurrencyService::class);
                        if ($service->updateExchangeRates()) {
                            Notification::make()
                                ->title('Exchange rates updated')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to update rates')
                                ->body('Check your API key in settings')
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('seed_currencies')
                    ->label('Add Common Currencies')
                    ->icon('heroicon-o-plus-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Add Common Currencies')
                    ->modalDescription('This will add common world currencies that are not already in the database.')
                    ->action(function () {
                        $added = 0;
                        foreach (Currency::getCommonCurrencies() as $data) {
                            if (!Currency::where('code', $data['code'])->exists()) {
                                Currency::create($data);
                                $added++;
                            }
                        }
                        Notification::make()
                            ->title("{$added} currencies added")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
