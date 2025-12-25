<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AffiliateResource\Pages;
use App\Models\Affiliate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Affiliate Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('paypal_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('commission_rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(10)
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Earnings')
                    ->schema([
                        Forms\Components\TextInput::make('total_earnings')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('pending_earnings')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('paid_earnings')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('total_referrals')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('successful_referrals')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Code copied'),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_earnings')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pending_earnings')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('successful_referrals')
                    ->label('Conversions')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'suspended',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->action(fn (Affiliate $record) => $record->approve())
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Affiliate $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('suspend')
                    ->action(fn (Affiliate $record) => $record->suspend())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Affiliate $record) => $record->status === 'active'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->action(fn ($records) => $records->each->approve())
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliates::route('/'),
            'create' => Pages\CreateAffiliate::route('/create'),
            'edit' => Pages\EditAffiliate::route('/{record}/edit'),
        ];
    }
}
