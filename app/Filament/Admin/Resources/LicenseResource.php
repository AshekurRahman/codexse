<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LicenseResource\Pages;
use App\Models\License;
use App\Services\LicenseService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'license_key';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'active')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('License Information')
                    ->schema([
                        Forms\Components\TextInput::make('license_key')
                            ->disabled()
                            ->columnSpan(2),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'expired' => 'Expired',
                                'revoked' => 'Revoked',
                            ])
                            ->required(),
                        Forms\Components\Select::make('license_type')
                            ->options([
                                'regular' => 'Regular',
                                'extended' => 'Extended',
                                'unlimited' => 'Unlimited',
                            ])
                            ->required(),
                    ])->columns(4),

                Forms\Components\Section::make('Associations')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\Select::make('order_item_id')
                            ->relationship('orderItem', 'id')
                            ->disabled()
                            ->label('Order Item'),
                    ])->columns(3),

                Forms\Components\Section::make('Activation Settings')
                    ->schema([
                        Forms\Components\TextInput::make('activations_count')
                            ->numeric()
                            ->disabled()
                            ->label('Current Activations'),
                        Forms\Components\TextInput::make('max_activations')
                            ->numeric()
                            ->required()
                            ->helperText('Set to 0 for unlimited activations')
                            ->label('Max Activations'),
                        Forms\Components\DateTimePicker::make('activated_at')
                            ->disabled()
                            ->label('First Activated'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiration Date'),
                    ])->columns(4),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('License key copied')
                    ->fontFamily('mono')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('license_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'regular' => 'gray',
                        'extended' => 'info',
                        'unlimited' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'expired' => 'gray',
                        'revoked' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('activations_count')
                    ->label('Activations')
                    ->formatStateUsing(fn (License $record): string =>
                        $record->max_activations === 0
                            ? "{$record->activations_count} / ∞"
                            : "{$record->activations_count} / {$record->max_activations}"
                    ),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                        'revoked' => 'Revoked',
                    ]),
                Tables\Filters\SelectFilter::make('license_type')
                    ->options([
                        'regular' => 'Regular',
                        'extended' => 'Extended',
                        'unlimited' => 'Unlimited',
                    ]),
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Product'),
            ])
            ->actions([
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (License $record): bool => $record->status === 'active')
                    ->action(function (License $record): void {
                        app(LicenseService::class)->suspend($record, 'Suspended from admin panel');
                        Notification::make()
                            ->title('License suspended')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reactivate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (License $record): bool => $record->status === 'suspended')
                    ->action(function (License $record): void {
                        app(LicenseService::class)->reactivate($record);
                        Notification::make()
                            ->title('License reactivated')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This action cannot be undone. All activations will be deactivated.')
                    ->visible(fn (License $record): bool => in_array($record->status, ['active', 'suspended']))
                    ->action(function (License $record): void {
                        app(LicenseService::class)->revoke($record, 'Revoked from admin panel');
                        Notification::make()
                            ->title('License revoked')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\Action::make('regenerate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('This will generate a new license key. The old key will no longer work.')
                    ->action(function (License $record): void {
                        $newKey = app(LicenseService::class)->regenerateKey($record);
                        Notification::make()
                            ->title('License key regenerated')
                            ->body("New key: {$newKey}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('suspend_selected')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $licenseService = app(LicenseService::class);
                            foreach ($records as $record) {
                                if ($record->status === 'active') {
                                    $licenseService->suspend($record, 'Bulk suspended from admin');
                                }
                            }
                            Notification::make()
                                ->title('Selected licenses suspended')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListLicenses::route('/'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
