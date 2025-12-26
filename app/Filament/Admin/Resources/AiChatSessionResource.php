<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AiChatSessionResource\Pages;
use App\Models\AiChatSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AiChatSessionResource extends Resource
{
    protected static ?string $model = AiChatSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Support';

    protected static ?string $navigationLabel = 'AI Conversations';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Session Details')
                    ->schema([
                        Forms\Components\TextInput::make('session_id')
                            ->label('Session ID')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Guest User'),
                        Forms\Components\TextInput::make('guest_name')
                            ->label('Guest Name'),
                        Forms\Components\TextInput::make('guest_email')
                            ->label('Guest Email')
                            ->email(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'closed' => 'Closed',
                                'archived' => 'Archived',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('message_count')
                            ->label('Messages')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_tokens_used')
                            ->label('Total Tokens')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('User / Guest')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                              ->orWhere('guest_name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('Guest')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray' => 'closed',
                        'secondary' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('message_count')
                    ->label('Messages')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_tokens_used')
                    ->label('Tokens')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'closed' => 'Closed',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\Filter::make('authenticated')
                    ->label('Authenticated Users')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id')),
                Tables\Filters\Filter::make('guests')
                    ->label('Guest Users')
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (AiChatSession $record): string =>
                        static::getUrl('view', ['record' => $record])
                    ),
                Tables\Actions\Action::make('close')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (AiChatSession $record) => $record->close())
                    ->visible(fn (AiChatSession $record) => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('close_selected')
                        ->label('Close Selected')
                        ->action(fn ($records) => $records->each->close())
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Archive Selected')
                        ->action(fn ($records) => $records->each->archive())
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiChatSessions::route('/'),
            'edit' => Pages\EditAiChatSession::route('/{record}/edit'),
            'view' => Pages\ViewAiChatSession::route('/{record}'),
        ];
    }
}
