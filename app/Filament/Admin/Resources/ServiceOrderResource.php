<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ServiceOrderResource\Pages;
use App\Filament\Admin\Resources\ServiceOrderResource\RelationManagers;
use App\Models\ServiceOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceOrderResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Service Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('buyer_id')
                            ->relationship('buyer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? "Seller #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('service_id')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('platform_fee')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('seller_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(3),

                Forms\Components\Section::make('Delivery Settings')
                    ->schema([
                        Forms\Components\TextInput::make('delivery_days')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('revisions_allowed')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('revisions_used')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'requirements_submitted' => 'Requirements Submitted',
                                'in_progress' => 'In Progress',
                                'delivered' => 'Delivered',
                                'revision_requested' => 'Revision Requested',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'disputed' => 'Disputed',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->visible(fn ($get) => $get('status') === 'cancelled'),
                    ]),

                Forms\Components\Section::make('Timeline')
                    ->schema([
                        Forms\Components\DateTimePicker::make('due_at'),
                        Forms\Components\DateTimePicker::make('started_at'),
                        Forms\Components\DateTimePicker::make('delivered_at'),
                        Forms\Components\DateTimePicker::make('completed_at'),
                        Forms\Components\DateTimePicker::make('cancelled_at'),
                        Forms\Components\DateTimePicker::make('auto_complete_at'),
                    ])->columns(3)->collapsed(),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('delivery_notes')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('completion_notes')
                            ->columnSpanFull(),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.user.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->limit(20)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'requirements_submitted' => 'info',
                        'in_progress' => 'warning',
                        'delivered' => 'info',
                        'revision_requested' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'disputed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Due'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'requirements_submitted' => 'Requirements Submitted',
                        'in_progress' => 'In Progress',
                        'delivered' => 'Delivered',
                        'revision_requested' => 'Revision Requested',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'disputed' => 'Disputed',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListServiceOrders::route('/'),
            'create' => Pages\CreateServiceOrder::route('/create'),
            'edit' => Pages\EditServiceOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
