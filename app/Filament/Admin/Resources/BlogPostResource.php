<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogPostResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Posts';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Content')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->helperText('Brief summary for listings and SEO'),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('blog-attachments'),
                            ]),

                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(70)
                                    ->helperText('Leave empty to use post title'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(160)
                                    ->rows(2)
                                    ->helperText('Leave empty to use excerpt'),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->helperText('Comma-separated keywords'),
                                Forms\Components\FileUpload::make('og_image')
                                    ->label('Social Share Image')
                                    ->image()
                                    ->directory('blog-og')
                                    ->helperText('Leave empty to use featured image'),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'scheduled' => 'Scheduled',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->default(now())
                                    ->visible(fn ($get) => in_array($get('status'), ['published', 'scheduled'])),
                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label('Schedule For')
                                    ->visible(fn ($get) => $get('status') === 'scheduled'),
                            ]),

                        Forms\Components\Section::make('Details')
                            ->schema([
                                Forms\Components\Select::make('blog_category_id')
                                    ->label('Category')
                                    ->options(BlogCategory::active()->ordered()->pluck('name', 'id'))
                                    ->searchable(),
                                Forms\Components\Select::make('user_id')
                                    ->label('Author')
                                    ->relationship('author', 'name')
                                    ->default(auth()->id())
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TagsInput::make('tags')
                                    ->separator(','),
                            ]),

                        Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->directory('blog-images')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),

                        Forms\Components\Section::make('Options')
                            ->schema([
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured Post'),
                                Forms\Components\Toggle::make('allow_comments')
                                    ->label('Allow Comments')
                                    ->default(true),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                    ]),
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->options(BlogCategory::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (BlogPost $record): string => url("/blog/{$record->slug}"))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab()
                    ->visible(fn (BlogPost $record) => $record->status === 'published'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ])))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
