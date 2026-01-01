<?php

namespace App\Filament\Admin\Resources\WalletResource\RelationManagers;

use App\Models\WalletTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $recordTitleAttribute = 'reference';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(WalletTransaction::getTypeOptions())
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(WalletTransaction::getStatusOptions())
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->fontFamily('mono')
                    ->size('sm'),

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
                    ->color(fn ($record) => $record->is_positive ? 'success' : 'danger'),

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
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
