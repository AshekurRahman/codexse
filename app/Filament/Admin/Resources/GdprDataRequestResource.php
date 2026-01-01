<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GdprDataRequestResource\Pages;
use App\Models\GdprDataRequest;
use App\Services\GdprService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class GdprDataRequestResource extends Resource
{
    protected static ?string $model = GdprDataRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Security';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'request_number';

    protected static ?string $modelLabel = 'GDPR Request';

    protected static ?string $pluralModelLabel = 'GDPR Requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\TextInput::make('request_number')
                            ->disabled(),
                        Forms\Components\Select::make('type')
                            ->options(GdprDataRequest::TYPES)
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options(GdprDataRequest::STATUSES)
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->label('User'),
                    ])->columns(4),

                Forms\Components\Section::make('Processing')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_number')
                    ->label('Request #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GdprDataRequest::TYPES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'export' => 'info',
                        'deletion' => 'danger',
                        'rectification' => 'warning',
                        'restriction' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('identity_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(GdprDataRequest::STATUSES)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(GdprDataRequest::TYPES)
                    ->multiple(),
                Tables\Filters\Filter::make('pending')
                    ->label('Pending Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('process_export')
                    ->label('Process Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (GdprDataRequest $record) => $record->type === 'export' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Process Data Export')
                    ->modalDescription('This will compile and generate the user\'s data export. The user will be notified when complete.')
                    ->action(function (GdprDataRequest $record) {
                        try {
                            app(GdprService::class)->processExportRequest($record);
                            Notification::make()
                                ->title('Export processed successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('process_deletion')
                    ->label('Process Deletion')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (GdprDataRequest $record) => $record->type === 'deletion' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Process Account Deletion')
                    ->modalDescription('This will permanently anonymize the user\'s data. This action cannot be undone!')
                    ->action(function (GdprDataRequest $record) {
                        try {
                            app(GdprService::class)->processDeletionRequest($record);
                            Notification::make()
                                ->title('Account deletion processed')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Deletion failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (GdprDataRequest $record) => in_array($record->status, ['pending', 'processing']))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (GdprDataRequest $record, array $data) {
                        $record->reject($data['reason']);
                        Notification::make()
                            ->title('Request rejected')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('process_exports')
                        ->label('Process Selected Exports')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $processed = 0;
                            foreach ($records as $record) {
                                if ($record->type === 'export' && $record->status === 'pending') {
                                    try {
                                        app(GdprService::class)->processExportRequest($record);
                                        $processed++;
                                    } catch (\Exception $e) {
                                        // Continue with other records
                                    }
                                }
                            }
                            Notification::make()
                                ->title("{$processed} export(s) processed")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Request Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('request_number')
                            ->label('Request Number')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => GdprDataRequest::TYPES[$state] ?? $state),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('identity_verified')
                            ->label('Identity Verified')
                            ->boolean(),
                    ])->columns(4),

                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Name'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user.created_at')
                            ->label('Account Created')
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Request Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('reason')
                            ->label('User Reason')
                            ->default('No reason provided')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('data_categories')
                            ->label('Requested Data Categories')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return 'All categories';
                                return collect($state)->map(fn ($cat) => GdprDataRequest::DATA_CATEGORIES[$cat] ?? $cat)->join(', ');
                            })
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Processing Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('processor.name')
                            ->label('Processed By')
                            ->default('Not processed'),
                        Infolists\Components\TextEntry::make('processed_at')
                            ->label('Processing Started')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Completed At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->default('No notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Export Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('export_file_path')
                            ->label('Export File'),
                        Infolists\Components\TextEntry::make('export_expires_at')
                            ->label('Expires At')
                            ->dateTime(),
                    ])->columns(2)
                    ->visible(fn (GdprDataRequest $record) => $record->type === 'export' && $record->export_file_path),

                Infolists\Components\Section::make('Timeline')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGdprDataRequests::route('/'),
            'view' => Pages\ViewGdprDataRequest::route('/{record}'),
            'edit' => Pages\EditGdprDataRequest::route('/{record}/edit'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'processor']);
    }
}
