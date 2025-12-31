<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SellerVerificationResource\Pages;
use App\Models\SellerVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SellerVerificationResource extends Resource
{
    protected static ?string $model = SellerVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Seller Verifications';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'under_review'])->count();
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
                Forms\Components\Section::make('Verification Request')
                    ->schema([
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'store_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('verification_type')
                            ->options(SellerVerification::getVerificationTypeOptions())
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('document_type')
                            ->options(SellerVerification::getDocumentTypeOptions())
                            ->disabled(),
                        Forms\Components\TextInput::make('document_number')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->disabled(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->disabled(),
                        Forms\Components\TextInput::make('country')
                            ->disabled(),
                        Forms\Components\Textarea::make('address')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('document_front')
                            ->label('Document Front')
                            ->image()
                            ->disabled()
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('document_back')
                            ->label('Document Back')
                            ->image()
                            ->disabled()
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('selfie_with_document')
                            ->label('Selfie with Document')
                            ->image()
                            ->disabled()
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Review')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(SellerVerification::getStatusOptions())
                            ->required()
                            ->live(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->helperText('Required when rejecting a verification request')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'rejected')
                            ->required(fn (Forms\Get $get) => $get('status') === 'rejected'),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Internal Notes')
                            ->helperText('These notes are only visible to admins'),
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Verification Expires At')
                            ->helperText('Leave empty for permanent verification')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'approved'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.store_name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'identity' => 'info',
                        'business' => 'warning',
                        'address' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('document_type')
                    ->formatStateUsing(fn (?string $state): string => $state ? SellerVerification::getDocumentTypeOptions()[$state] ?? $state : '-'),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SellerVerification::getStatusOptions()),
                Tables\Filters\SelectFilter::make('verification_type')
                    ->options(SellerVerification::getVerificationTypeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('start_review')
                    ->label('Start Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->markUnderReview();
                        Notification::make()
                            ->title('Verification marked as under review')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Verification')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notes (optional)'),
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Verification Expires At (optional)')
                            ->helperText('Leave empty for permanent verification'),
                    ])
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->action(function ($record, array $data) {
                        $expiresAt = $data['expires_at'] ? new \DateTime($data['expires_at']) : null;
                        $record->approve(auth()->user(), $data['admin_notes'], $expiresAt);
                        Notification::make()
                            ->title('Verification approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Verification')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Internal Notes (optional)'),
                    ])
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->action(function ($record, array $data) {
                        $record->reject(auth()->user(), $data['rejection_reason'], $data['admin_notes']);
                        Notification::make()
                            ->title('Verification rejected')
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
                Infolists\Components\Section::make('Seller Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('seller.store_name')
                            ->label('Store Name'),
                        Infolists\Components\TextEntry::make('seller.user.name')
                            ->label('Owner Name'),
                        Infolists\Components\TextEntry::make('seller.user.email')
                            ->label('Email'),
                    ])->columns(3),

                Infolists\Components\Section::make('Verification Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('verification_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'identity' => 'info',
                                'business' => 'warning',
                                'address' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('document_type')
                            ->formatStateUsing(fn (?string $state): string => $state ? SellerVerification::getDocumentTypeOptions()[$state] ?? $state : '-'),
                        Infolists\Components\TextEntry::make('document_number'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'under_review' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(4),

                Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('full_name'),
                        Infolists\Components\TextEntry::make('date_of_birth')
                            ->date(),
                        Infolists\Components\TextEntry::make('country'),
                        Infolists\Components\TextEntry::make('address')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Submitted Documents')
                    ->schema([
                        Infolists\Components\ImageEntry::make('document_front')
                            ->label('Document Front')
                            ->height(300)
                            ->extraImgAttributes(['class' => 'rounded-lg']),
                        Infolists\Components\ImageEntry::make('document_back')
                            ->label('Document Back')
                            ->height(300)
                            ->extraImgAttributes(['class' => 'rounded-lg']),
                        Infolists\Components\ImageEntry::make('selfie_with_document')
                            ->label('Selfie with Document')
                            ->height(300)
                            ->extraImgAttributes(['class' => 'rounded-lg'])
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('Review Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('reviewer.name')
                            ->label('Reviewed By'),
                        Infolists\Components\TextEntry::make('reviewed_at')
                            ->label('Reviewed At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->visible(fn ($record) => $record->status === 'rejected')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull(),
                    ])->columns(3)
                    ->visible(fn ($record) => $record->reviewed_at !== null),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellerVerifications::route('/'),
            'view' => Pages\ViewSellerVerification::route('/{record}'),
        ];
    }
}
