<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionPlanResource\Pages;
use App\Filament\Admin\Resources\SubscriptionPlanResource\RelationManagers;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Service;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Leave blank to auto-generate'),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('seller_id')
                            ->label('Seller')
                            ->options(Seller::where('status', 'approved')->pluck('store_name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->reactive(),

                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->options(Service::where('status', 'published')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->reactive(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0),

                        Forms\Components\Select::make('billing_period')
                            ->required()
                            ->options([
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'yearly' => 'Yearly',
                            ])
                            ->default('monthly'),

                        Forms\Components\TextInput::make('billing_interval')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->helperText('Number of billing periods per cycle'),

                        Forms\Components\TextInput::make('trial_days')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Number of free trial days'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Stripe Integration')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_product_id')
                            ->label('Stripe Product ID')
                            ->maxLength(255)
                            ->helperText('Product ID from Stripe Dashboard'),

                        Forms\Components\TextInput::make('stripe_price_id')
                            ->label('Stripe Price ID')
                            ->maxLength(255)
                            ->helperText('Price ID from Stripe Dashboard (required for checkout)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Limits & Features')
                    ->schema([
                        Forms\Components\TextInput::make('max_downloads')
                            ->numeric()
                            ->nullable()
                            ->helperText('Leave blank for unlimited'),

                        Forms\Components\TextInput::make('max_requests')
                            ->numeric()
                            ->nullable()
                            ->helperText('Leave blank for unlimited'),

                        Forms\Components\TagsInput::make('features')
                            ->placeholder('Add feature')
                            ->helperText('Features included in this plan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Plan Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('slug'),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('seller.store_name')
                            ->label('Seller'),
                        Infolists\Components\TextEntry::make('product.name')
                            ->label('Product'),
                        Infolists\Components\TextEntry::make('service.name')
                            ->label('Service'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Pricing')
                    ->schema([
                        Infolists\Components\TextEntry::make('formatted_price')
                            ->label('Price'),
                        Infolists\Components\TextEntry::make('billing_period_label')
                            ->label('Billing Period'),
                        Infolists\Components\TextEntry::make('billing_interval')
                            ->label('Billing Interval'),
                        Infolists\Components\TextEntry::make('trial_days')
                            ->label('Trial Days')
                            ->suffix(' days'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Stripe Integration')
                    ->schema([
                        Infolists\Components\TextEntry::make('stripe_product_id')
                            ->label('Stripe Product ID')
                            ->placeholder('Not set'),
                        Infolists\Components\TextEntry::make('stripe_price_id')
                            ->label('Stripe Price ID')
                            ->placeholder('Not set'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Limits & Features')
                    ->schema([
                        Infolists\Components\TextEntry::make('max_downloads')
                            ->label('Max Downloads')
                            ->placeholder('Unlimited'),
                        Infolists\Components\TextEntry::make('max_requests')
                            ->label('Max Requests')
                            ->placeholder('Unlimited'),
                        Infolists\Components\TextEntry::make('features')
                            ->label('Features')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Status & Stats')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Featured')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')
                            ->label('Sort Order'),
                        Infolists\Components\TextEntry::make('subscriptions_count')
                            ->label('Active Subscribers')
                            ->state(fn ($record) => $record->subscriptions()->whereIn('status', ['active', 'trialing'])->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('seller.store_name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('formatted_price')
                    ->label('Price')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('price', $direction)),

                Tables\Columns\TextColumn::make('billing_period_label')
                    ->label('Billing'),

                Tables\Columns\TextColumn::make('trial_days')
                    ->label('Trial')
                    ->suffix(' days')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Subscribers')
                    ->counts('subscriptions')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Tables\Filters\SelectFilter::make('billing_period')
                    ->options([
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'yearly' => 'Yearly',
                    ]),

                Tables\Filters\SelectFilter::make('seller_id')
                    ->label('Seller')
                    ->options(Seller::where('status', 'approved')->pluck('store_name', 'id'))
                    ->searchable(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'view' => Pages\ViewSubscriptionPlan::route('/{record}'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
