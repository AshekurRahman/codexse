<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 15;

    protected static ?string $navigationLabel = 'API Keys';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Key Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Key Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mobile App, Third-party Integration'),

                        Forms\Components\Select::make('user_id')
                            ->label('Associated User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Optional: Associate this key with a user'),

                        Forms\Components\TextInput::make('key')
                            ->label('API Key')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('Leave empty for no expiration'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Permissions & Limits')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->options(ApiKey::PERMISSIONS)
                            ->columns(2)
                            ->helperText('Leave empty to grant all permissions'),

                        Forms\Components\TextInput::make('rate_limit')
                            ->label('Daily Rate Limit')
                            ->numeric()
                            ->default(1000)
                            ->suffix('requests/day')
                            ->required(),

                        Forms\Components\TagsInput::make('allowed_ips')
                            ->label('Allowed IP Addresses')
                            ->placeholder('Add IP address')
                            ->helperText('Leave empty to allow all IPs'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('API Key')
                    ->copyable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->key),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('None')
                    ->sortable(),

                Tables\Columns\TextColumn::make('requests_count')
                    ->label('Requests')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => $record->status_color),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('regenerate')
                    ->label('Regenerate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate API Key')
                    ->modalDescription('This will generate a new secret. The old secret will no longer work. Continue?')
                    ->action(function (ApiKey $record) {
                        $newSecret = ApiKey::generateSecret();
                        $record->update([
                            'secret_hash' => Hash::make($newSecret),
                        ]);

                        Notification::make()
                            ->title('API Key Regenerated')
                            ->body("New Secret: {$newSecret}")
                            ->warning()
                            ->persistent()
                            ->send();
                    }),

                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (ApiKey $record) => $record->is_active ? 'Disable' : 'Enable')
                    ->icon(fn (ApiKey $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (ApiKey $record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (ApiKey $record) {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? 'API Key Enabled' : 'API Key Disabled')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
