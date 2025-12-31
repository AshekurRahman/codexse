<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobPostingResource\Pages;
use App\Filament\Admin\Resources\JobPostingResource\RelationManagers;
use App\Models\JobPosting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Jobs';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Job Postings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('requirements')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('skills_required')
                            ->placeholder('Add skills'),
                    ]),

                Forms\Components\Section::make('Budget & Timeline')
                    ->schema([
                        Forms\Components\Select::make('budget_type')
                            ->options([
                                'fixed' => 'Fixed Price',
                                'hourly' => 'Hourly Rate',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('budget_min')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('budget_max')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\DatePicker::make('deadline'),
                        Forms\Components\Select::make('duration_type')
                            ->options([
                                'less_than_week' => 'Less than a week',
                                'one_to_four_weeks' => '1-4 weeks',
                                'one_to_three_months' => '1-3 months',
                                'three_to_six_months' => '3-6 months',
                                'more_than_six_months' => 'More than 6 months',
                            ]),
                        Forms\Components\Select::make('experience_level')
                            ->options([
                                'entry' => 'Entry Level',
                                'intermediate' => 'Intermediate',
                                'expert' => 'Expert',
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('Status & Visibility')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        Forms\Components\Select::make('visibility')
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private (Invite Only)',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\DateTimePicker::make('closes_at'),
                    ])->columns(2),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('proposals_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('budget_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'info',
                        'hourly' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('budget_min')
                    ->money()
                    ->sortable()
                    ->label('Min Budget'),
                Tables\Columns\TextColumn::make('budget_max')
                    ->money()
                    ->sortable()
                    ->label('Max Budget')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'success',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('proposals_count')
                    ->numeric()
                    ->sortable()
                    ->label('Proposals'),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('budget_type')
                    ->options([
                        'fixed' => 'Fixed Price',
                        'hourly' => 'Hourly Rate',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListJobPostings::route('/'),
            'create' => Pages\CreateJobPosting::route('/create'),
            'edit' => Pages\EditJobPosting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
