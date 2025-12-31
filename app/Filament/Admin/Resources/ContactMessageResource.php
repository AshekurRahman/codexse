<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        return (string) ContactMessage::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return ContactMessage::where('status', 'new')->count() > 0 ? 'danger' : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->disabled()
                            ->rows(6),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Admin')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(ContactMessage::getStatuses())
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(4)
                            ->placeholder('Internal notes about this message...'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled()
                            ->label('Received At'),
                        Forms\Components\DateTimePicker::make('replied_at')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'read' => 'warning',
                        'replied' => 'success',
                        'closed' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Account')
                    ->default('Guest')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ContactMessage::getStatuses()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markRead')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('warning')
                    ->action(fn (ContactMessage $record) => $record->markAsRead())
                    ->visible(fn (ContactMessage $record) => $record->status === 'new'),
                Tables\Actions\Action::make('markReplied')
                    ->label('Mark Replied')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (ContactMessage $record) => $record->markAsReplied())
                    ->visible(fn (ContactMessage $record) => in_array($record->status, ['new', 'read'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
