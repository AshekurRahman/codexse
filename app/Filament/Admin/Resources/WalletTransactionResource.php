<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WalletTransactionResource\Pages;
use App\Models\WalletTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('wallet_id')
                            ->relationship('wallet', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' - ' . $record->formatted_balance)
                            ->searchable()
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('type')
                            ->options(WalletTransaction::getTypeOptions())
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->options(WalletTransaction::getStatusOptions())
                            ->required(),

                        Forms\Components\TextInput::make('reference')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_id')
                            ->label('Payment ID')
                            ->disabled(),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Transaction Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('type_label')
                            ->label('Type')
                            ->badge()
                            ->color(fn ($record) => match ($record->type) {
                                'deposit', 'refund', 'bonus', 'transfer_in' => 'success',
                                'withdrawal', 'purchase', 'transfer_out' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('formatted_amount')
                            ->label('Amount')
                            ->color(fn ($record) => $record->is_positive ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Balance Changes')
                    ->schema([
                        Infolists\Components\TextEntry::make('formatted_balance_before')
                            ->label('Balance Before'),
                        Infolists\Components\TextEntry::make('formatted_balance_after')
                            ->label('Balance After'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('User & Wallet')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('wallet.formatted_balance')
                            ->label('Current Wallet Balance'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('payment_id')
                            ->label('Payment ID')
                            ->placeholder('N/A')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->dateTime()
                            ->placeholder('Not completed'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type_label')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($record) => match ($record->type) {
                        'deposit', 'refund', 'bonus', 'transfer_in' => 'success',
                        'withdrawal', 'purchase', 'transfer_out' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Amount')
                    ->color(fn ($record) => $record->is_positive ? 'success' : 'danger')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('amount', $direction)),

                Tables\Columns\TextColumn::make('formatted_balance_after')
                    ->label('Balance After'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(WalletTransaction::getTypeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->options(WalletTransaction::getStatusOptions()),

                Tables\Filters\Filter::make('deposits')
                    ->label('Deposits Only')
                    ->query(fn (Builder $query) => $query->deposits()),

                Tables\Filters\Filter::make('purchases')
                    ->label('Purchases Only')
                    ->query(fn (Builder $query) => $query->purchases()),

                Tables\Filters\Filter::make('positive')
                    ->label('Positive Amounts')
                    ->query(fn (Builder $query) => $query->positive()),

                Tables\Filters\Filter::make('negative')
                    ->label('Negative Amounts')
                    ->query(fn (Builder $query) => $query->negative()),

                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListWalletTransactions::route('/'),
            'view' => Pages\ViewWalletTransaction::route('/{record}'),
            'edit' => Pages\EditWalletTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
