<?php

namespace App\Filament\Admin\Resources\SubscriptionPlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'trialing' => 'Trialing',
                        'past_due' => 'Past Due',
                        'paused' => 'Paused',
                        'canceled' => 'Canceled',
                        'expired' => 'Expired',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('current_period_start'),
                Forms\Components\DateTimePicker::make('current_period_end'),
            ]);
    }

    public function table(Table $table): Table
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

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'info' => 'trialing',
                        'warning' => fn ($state) => in_array($state, ['past_due', 'paused']),
                        'danger' => fn ($state) => in_array($state, ['canceled', 'expired']),
                    ]),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Renews/Expires')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('downloads_used')
                    ->label('Downloads')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('cancel_at_period_end')
                    ->label('Canceling')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'trialing' => 'Trialing',
                        'past_due' => 'Past Due',
                        'paused' => 'Paused',
                        'canceled' => 'Canceled',
                        'expired' => 'Expired',
                    ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->cancel())
                    ->visible(fn ($record) => $record->isActive() && !$record->cancel_at_period_end),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
