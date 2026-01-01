<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Filament\Admin\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('subscription_plan_id')
                            ->label('Plan')
                            ->options(SubscriptionPlan::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options(Subscription::getStatusOptions())
                            ->required()
                            ->default('active'),

                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing Period')
                    ->schema([
                        Forms\Components\DateTimePicker::make('current_period_start')
                            ->required(),

                        Forms\Components\DateTimePicker::make('current_period_end')
                            ->required(),

                        Forms\Components\Toggle::make('cancel_at_period_end')
                            ->label('Cancel at Period End'),

                        Forms\Components\DateTimePicker::make('canceled_at'),

                        Forms\Components\DateTimePicker::make('ended_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Usage')
                    ->schema([
                        Forms\Components\TextInput::make('downloads_used')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('requests_used')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Stripe Information')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_customer_id')
                            ->label('Stripe Customer ID')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Subscription Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('plan.name')
                            ->label('Plan'),
                        Infolists\Components\TextEntry::make('plan.formatted_price')
                            ->label('Price'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'trialing' => 'info',
                                'past_due', 'paused', 'incomplete' => 'warning',
                                'canceled', 'expired', 'incomplete_expired' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('trial_ends_at')
                            ->label('Trial Ends')
                            ->dateTime()
                            ->placeholder('No trial'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Billing Period')
                    ->schema([
                        Infolists\Components\TextEntry::make('current_period_start')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('current_period_end')
                            ->dateTime(),
                        Infolists\Components\IconEntry::make('cancel_at_period_end')
                            ->label('Cancel at Period End')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('canceled_at')
                            ->dateTime()
                            ->placeholder('Not canceled'),
                        Infolists\Components\TextEntry::make('ended_at')
                            ->dateTime()
                            ->placeholder('Not ended'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Usage')
                    ->schema([
                        Infolists\Components\TextEntry::make('downloads_used')
                            ->label('Downloads Used'),
                        Infolists\Components\TextEntry::make('requests_used')
                            ->label('Requests Used'),
                        Infolists\Components\TextEntry::make('plan.max_downloads')
                            ->label('Max Downloads')
                            ->placeholder('Unlimited'),
                        Infolists\Components\TextEntry::make('plan.max_requests')
                            ->label('Max Requests')
                            ->placeholder('Unlimited'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Stripe Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->placeholder('Not set')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('stripe_customer_id')
                            ->label('Stripe Customer ID')
                            ->placeholder('Not set')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
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

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan.formatted_price')
                    ->label('Price'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'info' => 'trialing',
                        'warning' => fn ($state) => in_array($state, ['past_due', 'paused', 'incomplete']),
                        'danger' => fn ($state) => in_array($state, ['canceled', 'expired', 'incomplete_expired']),
                    ]),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Renews On')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->badge()
                    ->color(fn ($state) => $state <= 7 ? 'warning' : 'success'),

                Tables\Columns\IconColumn::make('cancel_at_period_end')
                    ->label('Canceling')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Subscription::getStatusOptions()),

                Tables\Filters\SelectFilter::make('subscription_plan_id')
                    ->label('Plan')
                    ->options(SubscriptionPlan::pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('cancel_at_period_end')
                    ->label('Canceling'),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (7 days)')
                    ->query(fn (Builder $query) => $query->where('current_period_end', '<=', now()->addDays(7))->whereIn('status', ['active', 'trialing'])),

                Tables\Filters\Filter::make('past_due')
                    ->label('Past Due')
                    ->query(fn (Builder $query) => $query->where('status', 'past_due')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Subscription')
                    ->modalDescription('Are you sure you want to cancel this subscription? The user will lose access at the end of the billing period.')
                    ->action(fn (Subscription $record) => $record->cancel())
                    ->visible(fn (Subscription $record) => $record->isActive() && !$record->cancel_at_period_end),
                Tables\Actions\Action::make('resume')
                    ->label('Resume')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Subscription $record) => $record->resume())
                    ->visible(fn (Subscription $record) => $record->cancel_at_period_end),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['active', 'trialing'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
