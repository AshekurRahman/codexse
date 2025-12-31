<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DisputeResource\Pages;
use App\Filament\Admin\Resources\DisputeResource\RelationManagers;
use App\Models\Dispute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Escrow';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dispute Details')
                    ->schema([
                        Forms\Components\Select::make('escrow_transaction_id')
                            ->relationship('escrowTransaction', 'transaction_number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('initiated_by')
                            ->relationship('initiator', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Initiated By'),
                    ])->columns(2),

                Forms\Components\Section::make('Disputable Item')
                    ->schema([
                        Forms\Components\TextInput::make('disputable_type')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->label('Type'),
                        Forms\Components\TextInput::make('disputable_id')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->label('ID'),
                    ])->columns(2),

                Forms\Components\Section::make('Dispute Information')
                    ->schema([
                        Forms\Components\Select::make('reason')
                            ->options([
                                'not_as_described' => 'Not as Described',
                                'quality_issues' => 'Quality Issues',
                                'late_delivery' => 'Late Delivery',
                                'no_delivery' => 'No Delivery',
                                'communication_issues' => 'Communication Issues',
                                'refund_request' => 'Refund Request',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('evidence')
                            ->multiple()
                            ->directory('disputes/evidence')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status & Resolution')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'under_review' => 'Under Review',
                                'awaiting_response' => 'Awaiting Response',
                                'resolved_buyer_favor' => 'Resolved (Buyer Favor)',
                                'resolved_seller_favor' => 'Resolved (Seller Favor)',
                                'resolved_split' => 'Resolved (Split)',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        Forms\Components\Select::make('resolved_by')
                            ->relationship('resolver', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Resolved By'),
                        Forms\Components\DateTimePicker::make('resolved_at'),
                    ])->columns(3),

                Forms\Components\Section::make('Resolution Details')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('resolution_notes')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->prefix('$')
                            ->label('Refund to Buyer'),
                        Forms\Components\TextInput::make('seller_amount')
                            ->numeric()
                            ->prefix('$')
                            ->label('Release to Seller'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('escrowTransaction.transaction_number')
                    ->label('Transaction')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('initiator.name')
                    ->label('Initiated By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disputable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'under_review' => 'warning',
                        'awaiting_response' => 'info',
                        'resolved_buyer_favor' => 'success',
                        'resolved_seller_favor' => 'success',
                        'resolved_split' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->money()
                    ->sortable()
                    ->label('Refund')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('seller_amount')
                    ->money()
                    ->sortable()
                    ->label('Seller')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('resolver.name')
                    ->label('Resolved By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'under_review' => 'Under Review',
                        'awaiting_response' => 'Awaiting Response',
                        'resolved_buyer_favor' => 'Resolved (Buyer Favor)',
                        'resolved_seller_favor' => 'Resolved (Seller Favor)',
                        'resolved_split' => 'Resolved (Split)',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('reason')
                    ->options([
                        'not_as_described' => 'Not as Described',
                        'quality_issues' => 'Quality Issues',
                        'late_delivery' => 'Late Delivery',
                        'no_delivery' => 'No Delivery',
                        'communication_issues' => 'Communication Issues',
                        'refund_request' => 'Refund Request',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve_buyer')
                    ->label('Favor Buyer')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Dispute $record) => in_array($record->status, ['open', 'under_review', 'awaiting_response']))
                    ->form([
                        Forms\Components\Textarea::make('resolution_notes')
                            ->required(),
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                    ])
                    ->action(function (Dispute $record, array $data) {
                        $record->update([
                            'status' => 'resolved_buyer_favor',
                            'resolution_notes' => $data['resolution_notes'],
                            'refund_amount' => $data['refund_amount'],
                            'seller_amount' => 0,
                            'resolved_by' => auth()->id(),
                            'resolved_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('resolve_seller')
                    ->label('Favor Seller')
                    ->icon('heroicon-o-building-storefront')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Dispute $record) => in_array($record->status, ['open', 'under_review', 'awaiting_response']))
                    ->form([
                        Forms\Components\Textarea::make('resolution_notes')
                            ->required(),
                        Forms\Components\TextInput::make('seller_amount')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                    ])
                    ->action(function (Dispute $record, array $data) {
                        $record->update([
                            'status' => 'resolved_seller_favor',
                            'resolution_notes' => $data['resolution_notes'],
                            'refund_amount' => 0,
                            'seller_amount' => $data['seller_amount'],
                            'resolved_by' => auth()->id(),
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
            'index' => Pages\ListDisputes::route('/'),
            'create' => Pages\CreateDispute::route('/create'),
            'edit' => Pages\EditDispute::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['open', 'under_review', 'awaiting_response'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
