<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LiveChatResource\Pages;
use App\Models\LiveChat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LiveChatResource extends Resource
{
    protected static ?string $model = LiveChat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Chat History';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Chat Details')
                    ->schema([
                        Forms\Components\TextInput::make('visitor_name')
                            ->label('Visitor Name')
                            ->disabled(),

                        Forms\Components\TextInput::make('visitor_email')
                            ->label('Visitor Email')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->options(LiveChat::STATUSES)
                            ->disabled(),

                        Forms\Components\Select::make('department')
                            ->options(LiveChat::DEPARTMENTS)
                            ->disabled(),

                        Forms\Components\TextInput::make('subject')
                            ->disabled(),

                        Forms\Components\TextInput::make('rating')
                            ->disabled()
                            ->suffix('/ 5'),

                        Forms\Components\Textarea::make('feedback')
                            ->disabled()
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('Visitor')
                    ->searchable()
                    ->description(fn (LiveChat $record): string => $record->visitor_email ?? ''),

                Tables\Columns\TextColumn::make('department')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => LiveChat::DEPARTMENTS[$state] ?? $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'active' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->placeholder('Unassigned'),

                Tables\Columns\TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label('Messages'),

                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn (?int $state): string => $state ? str_repeat('★', $state) . str_repeat('☆', 5 - $state) : '-')
                    ->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ended_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Active'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LiveChat::STATUSES),

                Tables\Filters\SelectFilter::make('department')
                    ->options(LiveChat::DEPARTMENTS),

                Tables\Filters\Filter::make('has_rating')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('rating'))
                    ->label('Has Rating'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListLiveChats::route('/'),
            'view' => Pages\ViewLiveChat::route('/{record}'),
        ];
    }
}
