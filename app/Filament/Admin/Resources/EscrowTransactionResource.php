<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EscrowTransactionResource\Pages;
use App\Filament\Admin\Resources\EscrowTransactionResource\RelationManagers;
use App\Models\EscrowTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EscrowTransactionResource extends Resource
{
    protected static ?string $model = EscrowTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Escrow';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Escrow Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('payer_id')
                            ->relationship('payer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('payee_id')
                            ->relationship('payee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? "Seller #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Linked Item')
                    ->schema([
                        Forms\Components\TextInput::make('escrowable_type')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->label('Type'),
                        Forms\Components\TextInput::make('escrowable_id')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->label('ID'),
                    ])->columns(2),

                Forms\Components\Section::make('Amounts')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Total Amount'),
                        Forms\Components\TextInput::make('platform_fee')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('payee_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Seller Amount'),
                        Forms\Components\TextInput::make('currency')
                            ->required()
                            ->maxLength(3)
                            ->default('USD'),
                    ])->columns(4),

                Forms\Components\Section::make('Status & Stripe')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'held' => 'Held',
                                'released' => 'Released',
                                'refunded' => 'Refunded',
                                'disputed' => 'Disputed',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('stripe_payment_intent_id')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_transfer_id')
                            ->maxLength(255)
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Timeline')
                    ->schema([
                        Forms\Components\DateTimePicker::make('held_at'),
                        Forms\Components\DateTimePicker::make('release_requested_at'),
                        Forms\Components\DateTimePicker::make('released_at'),
                        Forms\Components\DateTimePicker::make('auto_release_at'),
                        Forms\Components\DateTimePicker::make('disputed_at'),
                        Forms\Components\DateTimePicker::make('resolved_at'),
                    ])->columns(3)->collapsed(),

                Forms\Components\Section::make('Dispute Information')
                    ->schema([
                        Forms\Components\Textarea::make('dispute_reason')
                            ->columnSpanFull(),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payer.name')
                    ->label('Payer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payee.name')
                    ->label('Payee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrowable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable()
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('platform_fee')
                    ->money()
                    ->sortable()
                    ->label('Fee')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'held' => 'warning',
                        'released' => 'success',
                        'refunded' => 'info',
                        'disputed' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('held_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Held')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('auto_release_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Auto Release')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'held' => 'Held',
                        'released' => 'Released',
                        'refunded' => 'Refunded',
                        'disputed' => 'Disputed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('release')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (EscrowTransaction $record) => $record->status === 'held')
                    ->action(function (EscrowTransaction $record) {
                        $record->update([
                            'status' => 'released',
                            'released_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (EscrowTransaction $record) => $record->status === 'held')
                    ->action(function (EscrowTransaction $record) {
                        $record->update([
                            'status' => 'refunded',
                            'resolved_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEscrowTransactions::route('/'),
            'create' => Pages\CreateEscrowTransaction::route('/create'),
            'edit' => Pages\EditEscrowTransaction::route('/{record}/edit'),
        ];
    }
}
