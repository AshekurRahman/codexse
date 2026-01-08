<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WalletResource\Pages;
use App\Filament\Admin\Resources\WalletResource\RelationManagers;
use App\Filament\Admin\Traits\HasResourceAuthorization;
use App\Models\User;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    protected static ?string $permissionName = 'wallet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wallet Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('balance')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),

                        Forms\Components\TextInput::make('pending_balance')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD - US Dollar',
                                'EUR' => 'EUR - Euro',
                                'GBP' => 'GBP - British Pound',
                            ])
                            ->default('USD')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Toggle::make('is_frozen')
                            ->label('Frozen')
                            ->default(false)
                            ->helperText('Frozen wallets cannot make transactions'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Wallet Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('formatted_balance')
                            ->label('Balance'),
                        Infolists\Components\TextEntry::make('formatted_pending_balance')
                            ->label('Pending Balance'),
                        Infolists\Components\TextEntry::make('currency')
                            ->label('Currency'),
                        Infolists\Components\TextEntry::make('last_transaction_at')
                            ->label('Last Transaction')
                            ->dateTime()
                            ->placeholder('No transactions'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_frozen')
                            ->label('Frozen')
                            ->boolean()
                            ->trueColor('danger')
                            ->falseColor('success'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('transactions_count')
                            ->label('Total Transactions')
                            ->state(fn ($record) => $record->transactions()->count()),
                        Infolists\Components\TextEntry::make('total_deposits')
                            ->label('Total Deposits')
                            ->state(fn ($record) => format_price($record->transactions()->deposits()->completed()->sum('amount'))),
                        Infolists\Components\TextEntry::make('total_purchases')
                            ->label('Total Purchases')
                            ->state(fn ($record) => format_price(abs($record->transactions()->purchases()->completed()->sum('amount')))),
                        Infolists\Components\TextEntry::make('total_withdrawals')
                            ->label('Total Withdrawals')
                            ->state(fn ($record) => format_price(abs($record->transactions()->withdrawals()->completed()->sum('amount')))),
                    ])
                    ->columns(4),
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

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('formatted_balance')
                    ->label('Balance')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('balance', $direction)),

                Tables\Columns\TextColumn::make('formatted_pending_balance')
                    ->label('Pending')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_frozen')
                    ->label('Frozen')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('last_transaction_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\TernaryFilter::make('is_frozen')
                    ->label('Frozen'),

                Tables\Filters\Filter::make('has_balance')
                    ->label('Has Balance')
                    ->query(fn (Builder $query) => $query->where('balance', '>', 0)),

                Tables\Filters\Filter::make('high_balance')
                    ->label('High Balance (>$100)')
                    ->query(fn (Builder $query) => $query->where('balance', '>', 100)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('add_funds')
                    ->label('Add Funds')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0.01),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Admin deposit'),
                    ])
                    ->action(function (Wallet $record, array $data) {
                        $record->deposit(
                            amount: (float) $data['amount'],
                            description: $data['description'] ?? 'Admin deposit',
                            paymentMethod: 'admin'
                        );

                        Notification::make()
                            ->title('Funds Added')
                            ->success()
                            ->body(format_price($data['amount']) . ' has been added to the wallet.')
                            ->send();
                    }),
                Tables\Actions\Action::make('freeze')
                    ->label('Freeze')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Wallet $record) => $record->update(['is_frozen' => true]))
                    ->visible(fn (Wallet $record) => !$record->is_frozen),
                Tables\Actions\Action::make('unfreeze')
                    ->label('Unfreeze')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Wallet $record) => $record->update(['is_frozen' => false]))
                    ->visible(fn (Wallet $record) => $record->is_frozen),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('balance', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('balance', '>', 0)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
