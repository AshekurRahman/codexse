<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('SUMMER20')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->action(fn ($set) => $set('code', strtoupper(Str::random(8))))
                            ),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Summer Sale 20% Off'),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->placeholder('Get 20% off on all products')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Discount Settings')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount ($)',
                            ])
                            ->default('percentage'),
                        Forms\Components\TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : '$'),
                        Forms\Components\TextInput::make('minimum_order')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Minimum order amount required'),
                        Forms\Components\TextInput::make('maximum_discount')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('No limit')
                            ->helperText('Maximum discount amount (for percentage coupons)'),
                    ])->columns(2),

                Forms\Components\Section::make('Usage Limits')
                    ->schema([
                        Forms\Components\TextInput::make('max_uses')
                            ->numeric()
                            ->placeholder('Unlimited')
                            ->helperText('Total times this coupon can be used'),
                        Forms\Components\TextInput::make('max_uses_per_user')
                            ->numeric()
                            ->default(1)
                            ->helperText('Times per customer'),
                        Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Validity')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Start Date'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiry Date'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage'
                        ? $record->value . '%'
                        : '$' . number_format($record->value, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($record) => $record->max_uses
                        ? "{$record->used_count}/{$record->max_uses}"
                        : $record->used_count)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at?->isPast() ? 'danger' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('copy')
                    ->icon('heroicon-o-clipboard')
                    ->action(fn () => null)
                    ->extraAttributes(fn ($record) => [
                        'x-data' => '{}',
                        'x-on:click' => "navigator.clipboard.writeText('{$record->code}')",
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
