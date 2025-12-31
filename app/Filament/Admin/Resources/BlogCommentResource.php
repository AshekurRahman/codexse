<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogCommentResource\Pages;
use App\Models\BlogComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogCommentResource extends Resource
{
    protected static ?string $model = BlogComment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Comments';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('blog_post_id')
                            ->relationship('post', 'title')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('author_name')
                            ->visible(fn ($get) => !$get('user_id')),
                        Forms\Components\TextInput::make('author_email')
                            ->email()
                            ->visible(fn ($get) => !$get('user_id')),
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Post')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_display_name')
                    ->label('Author')
                    ->searchable(['author_name', 'user.name']),
                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'spam' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (BlogComment $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (BlogComment $record) => $record->status !== 'approved')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('spam')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (BlogComment $record) => $record->update(['status' => 'spam']))
                    ->visible(fn (BlogComment $record) => $record->status !== 'spam')
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['status' => 'spam']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogComments::route('/'),
            'edit' => Pages\EditBlogComment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
