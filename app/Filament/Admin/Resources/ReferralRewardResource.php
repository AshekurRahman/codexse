<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReferralRewardResource\Pages;
use App\Models\ReferralReward;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferralRewardResource extends Resource
{
    protected static ?string $model = ReferralReward::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Referral Rewards';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('referred_user_id')
                            ->relationship('referredUser', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'signup' => 'Signup Bonus',
                                'purchase' => 'Purchase Commission',
                                'bonus' => 'Bonus',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'credited' => 'Credited',
                                'withdrawn' => 'Withdrawn',
                                'expired' => 'Expired',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referredUser.name')
                    ->label('Referred User')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'signup',
                        'primary' => 'purchase',
                        'warning' => 'bonus',
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->description),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'credited',
                        'info' => 'withdrawn',
                        'danger' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('credited_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'signup' => 'Signup Bonus',
                        'purchase' => 'Purchase Commission',
                        'bonus' => 'Bonus',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'credited' => 'Credited',
                        'withdrawn' => 'Withdrawn',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('credit')
                    ->label('Credit')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->credit()),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReferralRewards::route('/'),
            'create' => Pages\CreateReferralReward::route('/create'),
            'edit' => Pages\EditReferralReward::route('/{record}/edit'),
        ];
    }
}
