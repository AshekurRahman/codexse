<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobContractResource\Pages;
use App\Filament\Admin\Resources\JobContractResource\RelationManagers;
use App\Models\JobContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobContractResource extends Resource
{
    protected static ?string $model = JobContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Jobs';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Job Contracts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contract Details')
                    ->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('job_posting_id')
                            ->relationship('jobPosting', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
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

                Forms\Components\Section::make('Contract Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
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
                        Forms\Components\Select::make('payment_type')
                            ->options([
                                'fixed' => 'Fixed Price',
                                'milestone' => 'Milestone-based',
                                'hourly' => 'Hourly',
                            ])
                            ->required(),
                    ])->columns(4),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
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
                        Forms\Components\DateTimePicker::make('started_at')
                            ->required(),
                        Forms\Components\DateTimePicker::make('completed_at'),
                        Forms\Components\DateTimePicker::make('cancelled_at'),
                    ])->columns(3)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->label('Job')
                    ->limit(20)
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.user.name')
                    ->label('Freelancer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money()
                    ->sortable()
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('payment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'info',
                        'milestone' => 'success',
                        'hourly' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'disputed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Started'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'disputed' => 'Disputed',
                    ]),
                Tables\Filters\SelectFilter::make('payment_type')
                    ->options([
                        'fixed' => 'Fixed Price',
                        'milestone' => 'Milestone-based',
                        'hourly' => 'Hourly',
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
            'index' => Pages\ListJobContracts::route('/'),
            'create' => Pages\CreateJobContract::route('/create'),
            'edit' => Pages\EditJobContract::route('/{record}/edit'),
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
