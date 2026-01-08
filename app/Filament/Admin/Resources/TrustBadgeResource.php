<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TrustBadgeResource\Pages;
use App\Models\TrustBadge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrustBadgeResource extends Resource
{
    protected static ?string $model = TrustBadge::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Badge Details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Secure Payments'),

                    Forms\Components\TextInput::make('subtitle')
                        ->maxLength(255)
                        ->placeholder('e.g., 256-bit SSL'),

                    Forms\Components\Select::make('icon')
                        ->required()
                        ->searchable()
                        ->options([
                            'shield-check' => 'Shield Check',
                            'lock-closed' => 'Lock Closed',
                            'currency-dollar' => 'Currency Dollar',
                            'credit-card' => 'Credit Card',
                            'lifebuoy' => 'Lifebuoy (Support)',
                            'check-badge' => 'Check Badge',
                            'check-circle' => 'Check Circle',
                            'star' => 'Star',
                            'heart' => 'Heart',
                            'arrow-down-tray' => 'Download',
                            'arrow-path' => 'Refresh/Updates',
                            'clock' => 'Clock',
                            'bolt' => 'Bolt (Fast)',
                            'globe-alt' => 'Globe',
                            'users' => 'Users',
                            'hand-thumb-up' => 'Thumb Up',
                            'trophy' => 'Trophy',
                            'sparkles' => 'Sparkles',
                            'gift' => 'Gift',
                            'ticket' => 'Ticket',
                        ])
                        ->helperText('Select a Heroicon'),

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
                        ->default('success'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Visibility')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Show this badge on the homepage'),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtitle')
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
            'index' => Pages\ListTrustBadges::route('/'),
            'create' => Pages\CreateTrustBadge::route('/create'),
            'edit' => Pages\EditTrustBadge::route('/{record}/edit'),
        ];
    }
}
