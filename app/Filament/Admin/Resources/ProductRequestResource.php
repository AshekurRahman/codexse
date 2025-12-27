<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductRequestResource\Pages;
use App\Models\ProductRequest;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ProductRequestResource extends Resource
{
    protected static ?string $model = ProductRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Support';

    protected static ?string $navigationLabel = 'Product Requests';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Requester Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Registered User')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Product Details')
                    ->schema([
                        Forms\Components\TextInput::make('product_title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('urgency')
                            ->options(ProductRequest::getUrgencies())
                            ->required()
                            ->default('normal'),
                        Forms\Components\TextInput::make('budget_min')
                            ->numeric()
                            ->prefix('$')
                            ->label('Min Budget'),
                        Forms\Components\TextInput::make('budget_max')
                            ->numeric()
                            ->prefix('$')
                            ->label('Max Budget'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('features')
                            ->label('Required Features')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('reference_urls')
                            ->label('Reference URLs')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Assignment')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(ProductRequest::getStatuses())
                            ->required()
                            ->default('pending')
                            ->live(),
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'name')
                            ->label('Assigned To')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('fulfilled_by_product_id')
                            ->relationship('fulfilledByProduct', 'name')
                            ->label('Fulfilled by Product')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('status') === 'fulfilled'),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Reviewed At'),
                        Forms\Components\DateTimePicker::make('fulfilled_at')
                            ->label('Fulfilled At'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_title')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('name')
                    ->label('Requester')
                    ->searchable()
                    ->description(fn ($record) => $record->email),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('budget_range')
                    ->label('Budget')
                    ->getStateUsing(fn ($record) => $record->budget_range ?? '-'),
                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'approved' => 'primary',
                        'fulfilled' => 'success',
                        'rejected' => 'danger',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ProductRequest::getStatuses()),
                Tables\Filters\SelectFilter::make('urgency')
                    ->options(ProductRequest::getUrgencies()),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\Filter::make('pending')
                    ->label('Pending Only')
                    ->query(fn (Builder $query) => $query->where('status', 'pending'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('markReviewing')
                    ->label('Start Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'reviewing',
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Request marked as reviewing')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'reviewing']))
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Request approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('fulfill')
                    ->label('Mark Fulfilled')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->form([
                        Forms\Components\Select::make('product_id')
                            ->label('Select Product')
                            ->options(Product::where('status', 'published')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'fulfilled',
                            'fulfilled_by_product_id' => $data['product_id'],
                            'fulfilled_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Request fulfilled')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->isOpen())
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['reason'],
                        ]);
                        Notification::make()
                            ->title('Request rejected')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markReviewing')
                        ->label('Mark as Reviewing')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update([
                                'status' => 'reviewing',
                                'reviewed_at' => now(),
                            ]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('close')
                        ->label('Close Requests')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'closed'])))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductRequests::route('/'),
            'create' => Pages\CreateProductRequest::route('/create'),
            'view' => Pages\ViewProductRequest::route('/{record}'),
            'edit' => Pages\EditProductRequest::route('/{record}/edit'),
        ];
    }
}
