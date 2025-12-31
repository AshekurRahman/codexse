<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('category')
                            ->options([
                                'general' => 'General',
                                'newsletter' => 'Newsletter',
                                'promotional' => 'Promotional',
                                'transactional' => 'Transactional',
                                'announcement' => 'Announcement',
                            ])
                            ->default('general')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Template Content')
                    ->description('Use variables like {{subject}}, {{content}}, {{preview_text}}, {{unsubscribe_url}}, {{app_name}}, {{year}}')
                    ->schema([
                        Forms\Components\Textarea::make('html_content')
                            ->label('HTML Content')
                            ->required()
                            ->rows(20)
                            ->columnSpanFull()
                            ->helperText('Write your HTML email template. Use {{variable}} syntax for dynamic content.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'newsletter' => 'info',
                        'promotional' => 'success',
                        'transactional' => 'warning',
                        'announcement' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('campaigns_count')
                    ->label('Used In')
                    ->counts('campaigns')
                    ->suffix(' campaigns'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'newsletter' => 'Newsletter',
                        'promotional' => 'Promotional',
                        'transactional' => 'Transactional',
                        'announcement' => 'Announcement',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => "Preview: {$record->name}")
                    ->modalContent(fn ($record) => view('filament.components.email-preview', [
                        'content' => $record->renderContent([
                            'subject' => 'Sample Email Subject',
                            'preview_text' => 'This is a preview of your email...',
                            'content' => '<p>This is sample email content. Your actual campaign content will appear here.</p>',
                            'unsubscribe_url' => '#',
                            'app_name' => config('app.name'),
                            'year' => date('Y'),
                        ]),
                    ]))
                    ->modalWidth('5xl'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->name = $record->name . ' (Copy)';
                        $newTemplate->slug = Str::slug($newTemplate->name);
                        $newTemplate->save();
                    }),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
