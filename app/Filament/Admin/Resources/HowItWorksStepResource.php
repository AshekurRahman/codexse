<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HowItWorksStepResource\Pages;
use App\Models\HowItWorksStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HowItWorksStepResource extends Resource
{
    protected static ?string $model = HowItWorksStep::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'How It Works';

    protected static ?string $modelLabel = 'Step';

    protected static ?string $pluralModelLabel = 'How It Works Steps';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Step Details')
                ->schema([
                    Forms\Components\TextInput::make('step_number')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(fn () => HowItWorksStep::max('step_number') + 1)
                        ->helperText('Step number displayed in the circle'),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Browse & Discover'),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Brief description of this step')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Appearance')
                ->schema([
                    Forms\Components\Select::make('icon')
                        ->required()
                        ->searchable()
                        ->options([
                            'magnifying-glass' => 'Search/Discover',
                            'shopping-cart' => 'Shopping Cart',
                            'shield-check' => 'Shield/Security',
                            'credit-card' => 'Payment',
                            'arrow-down-tray' => 'Download',
                            'check-circle' => 'Complete/Check',
                            'rocket-launch' => 'Launch/Start',
                            'cursor-arrow-rays' => 'Click/Select',
                            'document-text' => 'Document',
                            'user-plus' => 'Sign Up',
                            'chat-bubble-left-right' => 'Communication',
                            'cog-6-tooth' => 'Settings',
                            'sparkles' => 'Magic/Quality',
                            'hand-thumb-up' => 'Approve/Like',
                            'gift' => 'Gift/Bonus',
                            'truck' => 'Delivery',
                            'star' => 'Rating/Review',
                            'bolt' => 'Fast/Quick',
                        ])
                        ->helperText('Icon displayed in the step circle'),

                    Forms\Components\Select::make('icon_color')
                        ->required()
                        ->options([
                            'primary' => 'Primary (Indigo)',
                            'accent' => 'Accent (Cyan)',
                            'success' => 'Success (Green)',
                            'warning' => 'Warning (Amber)',
                            'danger' => 'Danger (Red)',
                            'info' => 'Info (Blue)',
                        ])
                        ->default('primary')
                        ->helperText('Background color of the icon circle'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Secondary sort (step_number is primary)'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('step_number')
                    ->label('#')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('icon_color')
                    ->badge()
                    ->color(fn (string $state) => $state),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('step_number')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->color('gray')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHowItWorksSteps::route('/'),
            'create' => Pages\CreateHowItWorksStep::route('/create'),
            'edit' => Pages\EditHowItWorksStep::route('/{record}/edit'),
        ];
    }
}
