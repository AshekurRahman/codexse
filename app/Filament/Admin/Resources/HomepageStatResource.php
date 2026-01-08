<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HomepageStatResource\Pages;
use App\Models\HomepageStat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageStatResource extends Resource
{
    protected static ?string $model = HomepageStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Homepage Stats';

    protected static ?string $modelLabel = 'Stat';

    protected static ?string $pluralModelLabel = 'Homepage Stats';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Stat Details')
                ->schema([
                    Forms\Components\TextInput::make('label')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Products, Services, Sellers'),

                    Forms\Components\TextInput::make('value')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('e.g., 10,000'),

                    Forms\Components\TextInput::make('prefix')
                        ->maxLength(10)
                        ->placeholder('e.g., $ or empty'),

                    Forms\Components\TextInput::make('suffix')
                        ->maxLength(10)
                        ->placeholder('e.g., +, %, K, M'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Appearance')
                ->schema([
                    Forms\Components\Select::make('color')
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
                        ->helperText('Color of the dot indicator'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Preview')
                ->schema([
                    Forms\Components\Placeholder::make('preview')
                        ->label('Display Preview')
                        ->content(function ($get) {
                            $prefix = $get('prefix') ?? '';
                            $value = $get('value') ?? '0';
                            $suffix = $get('suffix') ?? '';
                            $label = $get('label') ?? 'Label';

                            return "{$prefix}{$value}{$suffix} {$label}";
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_value')
                    ->label('Value')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('color')
                    ->badge()
                    ->color(fn (string $state) => $state),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListHomepageStats::route('/'),
            'create' => Pages\CreateHomepageStat::route('/create'),
            'edit' => Pages\EditHomepageStat::route('/{record}/edit'),
        ];
    }
}
