<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SellerResource\Pages;
use App\Filament\Admin\Traits\HasResourceAuthorization;
use App\Models\Seller;
use App\Notifications\SellerApplicationApproved;
use App\Notifications\SellerApplicationRejected;
use App\Services\ActivityLogService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SellerResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'store_name';

    protected static ?string $permissionName = 'seller';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Store Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('store_name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('store_slug', Str::slug($state))),
                        Forms\Components\TextInput::make('store_slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\TextInput::make('logo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('banner')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'suspended' => 'Suspended',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Select::make('level')
                            ->options([
                                'bronze' => 'Bronze',
                                'silver' => 'Silver',
                                'gold' => 'Gold',
                                'platinum' => 'Platinum',
                            ])
                            ->required()
                            ->default('bronze'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified Seller'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Seller'),
                        Forms\Components\DateTimePicker::make('approved_at'),
                    ])->columns(2),

                Forms\Components\Section::make('Commission Override')
                    ->description('Leave empty to use the default commission rate from Commission Settings. Set a custom rate to override for this seller only.')
                    ->schema([
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Custom Commission Rate')
                            ->helperText('Override the default platform commission for this seller. Leave empty to use global settings.')
                            ->numeric()
                            ->suffix('%')
                            ->placeholder('Use default rate')
                            ->minValue(0)
                            ->maxValue(100),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'suspended' => 'danger',
                        'rejected' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bronze' => 'gray',
                        'silver' => 'info',
                        'gold' => 'warning',
                        'platinum' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->money('USD')
                    ->sortable()
                    ->label('Sales'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'bronze' => 'Bronze',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
                        'platinum' => 'Platinum',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Seller Application')
                    ->modalDescription('Are you sure you want to approve this seller application?')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved', 'approved_at' => now()]);

                        // Log the approval
                        ActivityLogService::logAdminSellerApproved($record, auth()->user());

                        // Send approval notification
                        try {
                            $record->user->notify(new SellerApplicationApproved($record));
                        } catch (\Exception $e) {
                            Log::warning('Failed to send seller approval notification: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->title('Seller application approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Seller Application')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->helperText('This will be sent to the applicant')
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        $record->update(['status' => 'rejected']);

                        // Log the rejection
                        ActivityLogService::logAdminSellerRejected($record, auth()->user(), $data['rejection_reason']);

                        // Send rejection notification
                        try {
                            $record->user->notify(new SellerApplicationRejected($record, $data['rejection_reason']));
                        } catch (\Exception $e) {
                            Log::warning('Failed to send seller rejection notification: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->title('Seller application rejected')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Store Information')
                    ->schema([
                        Infolists\Components\ImageEntry::make('logo')
                            ->label('Logo')
                            ->circular()
                            ->defaultImageUrl(fn ($record) => $record->logo_url),
                        Infolists\Components\TextEntry::make('store_name')
                            ->label('Store Name'),
                        Infolists\Components\TextEntry::make('store_slug')
                            ->label('Store Slug')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Owner'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('website')
                            ->label('Website')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Categories')
                    ->schema([
                        Infolists\Components\TextEntry::make('categories')
                            ->label('Selected Categories')
                            ->badge()
                            ->separator(',')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                        Infolists\Components\TextEntry::make('other_category')
                            ->label('Custom Category')
                            ->visible(fn ($record) => !empty($record->other_category)),
                    ])->columns(2),

                Infolists\Components\Section::make('Status & Level')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'suspended' => 'danger',
                                'rejected' => 'gray',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('level')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'bronze' => 'gray',
                                'silver' => 'info',
                                'gold' => 'warning',
                                'platinum' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('is_verified')
                            ->label('Verified')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Featured')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime(),
                    ])->columns(5),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('products_count')
                            ->label('Products'),
                        Infolists\Components\TextEntry::make('total_sales')
                            ->label('Total Sales')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('total_earnings')
                            ->label('Total Earnings')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('available_balance')
                            ->label('Available Balance')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('commission_rate')
                            ->label('Custom Commission')
                            ->suffix('%')
                            ->placeholder('Using default'),
                    ])->columns(5),

                Infolists\Components\Section::make('Vacation Mode')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_on_vacation')
                            ->label('On Vacation')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('vacation_message')
                            ->label('Vacation Message')
                            ->visible(fn ($record) => $record->is_on_vacation),
                        Infolists\Components\TextEntry::make('vacation_started_at')
                            ->label('Started')
                            ->dateTime()
                            ->visible(fn ($record) => $record->is_on_vacation),
                        Infolists\Components\TextEntry::make('vacation_ends_at')
                            ->label('Ends')
                            ->dateTime()
                            ->visible(fn ($record) => $record->is_on_vacation),
                    ])->columns(4)
                    ->visible(fn ($record) => $record->is_on_vacation),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Applied At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'view' => Pages\ViewSeller::route('/{record}'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
        ];
    }
}
