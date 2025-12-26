<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChatbotFaqResource\Pages;
use App\Models\ChatbotFaq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotFaqResource extends Resource
{
    protected static ?string $model = ChatbotFaq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Support';

    protected static ?string $navigationLabel = 'Chatbot FAQs';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('FAQ Details')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., How do I reset my password?')
                            ->helperText('The question users might ask'),

                        Forms\Components\RichEditor::make('answer')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                            ])
                            ->placeholder('Provide a helpful answer...')
                            ->helperText('The response the chatbot will give'),

                        Forms\Components\TextInput::make('keywords')
                            ->maxLength(255)
                            ->placeholder('password, reset, forgot, login')
                            ->helperText('Comma-separated keywords to help match user queries'),

                        Forms\Components\Select::make('category')
                            ->options([
                                'General' => 'General',
                                'Account' => 'Account & Login',
                                'Orders' => 'Orders & Purchases',
                                'Products' => 'Products',
                                'Payments' => 'Payments & Billing',
                                'Downloads' => 'Downloads & Licenses',
                                'Refunds' => 'Refunds & Returns',
                                'Technical' => 'Technical Support',
                            ])
                            ->placeholder('Select a category')
                            ->helperText('Group related FAQs together'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first when multiple FAQs match'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive FAQs will not be shown to users'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('keywords')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'General' => 'General',
                        'Account' => 'Account & Login',
                        'Orders' => 'Orders & Purchases',
                        'Products' => 'Products',
                        'Payments' => 'Payments & Billing',
                        'Downloads' => 'Downloads & Licenses',
                        'Refunds' => 'Refunds & Returns',
                        'Technical' => 'Technical Support',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatbotFaqs::route('/'),
            'create' => Pages\CreateChatbotFaq::route('/create'),
            'edit' => Pages\EditChatbotFaq::route('/{record}/edit'),
        ];
    }
}
