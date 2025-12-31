<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Marketplace';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'store_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Textarea::make('short_description')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('sale_price')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),

                Forms\Components\Section::make('Media & Files')
                    ->schema([
                        Forms\Components\TextInput::make('thumbnail')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->maxLength(255)
                            ->helperText('YouTube or Vimeo URL for product video'),
                        Forms\Components\TextInput::make('file_path')
                            ->label('Product File')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('file_size')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('file_type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('preview_url')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('demo_url')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Version & Compatibility')
                    ->schema([
                        Forms\Components\TextInput::make('version')
                            ->required()
                            ->default('1.0.0')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('changelog')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status & Visibility')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\TextInput::make('rejection_reason')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Product'),
                        Forms\Components\Toggle::make('has_variations')
                            ->label('Has Variations')
                            ->helperText('Enable to add multiple versions/tiers of this product')
                            ->live(),
                        Forms\Components\Toggle::make('is_trending')
                            ->label('Trending'),
                        Forms\Components\DateTimePicker::make('published_at'),
                    ])->columns(2),

                Forms\Components\Section::make('Product Variations')
                    ->description('Add different versions/tiers of this product (e.g., Basic, Pro, Enterprise)')
                    ->schema([
                        Forms\Components\Repeater::make('variations')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g., Basic, Pro, Enterprise'),
                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->placeholder('Brief description of this tier'),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('regular_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->helperText('Original price (for showing discounts)'),
                                Forms\Components\TagsInput::make('features')
                                    ->placeholder('Add features included in this tier')
                                    ->helperText('Press Enter to add each feature'),
                                Forms\Components\Select::make('license_type')
                                    ->options([
                                        'regular' => 'Regular License',
                                        'extended' => 'Extended License',
                                    ])
                                    ->default('regular'),
                                Forms\Components\TextInput::make('support_months')
                                    ->numeric()
                                    ->default(6)
                                    ->suffix('months')
                                    ->helperText('0 = Lifetime'),
                                Forms\Components\TextInput::make('updates_months')
                                    ->numeric()
                                    ->default(12)
                                    ->suffix('months')
                                    ->helperText('0 = Lifetime'),
                                Forms\Components\Toggle::make('is_default')
                                    ->label('Default Selection'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Variation')
                            ->addActionLabel('Add Variation'),
                    ])
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('has_variations')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('seller.store_name')
                    ->label('Seller')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Sales')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
