<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FraudAlertResource\Pages;
use App\Models\FraudAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class FraudAlertResource extends Resource
{
    protected static ?string $model = FraudAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Security';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'alert_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Alert Information')
                    ->schema([
                        Forms\Components\TextInput::make('alert_number')
                            ->disabled(),
                        Forms\Components\Select::make('type')
                            ->options(FraudAlert::TYPES)
                            ->disabled(),
                        Forms\Components\Select::make('severity')
                            ->options(FraudAlert::SEVERITIES)
                            ->disabled(),
                        Forms\Components\TextInput::make('risk_score')
                            ->suffix('/100')
                            ->disabled(),
                    ])->columns(4),

                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->label('User'),
                        Forms\Components\TextInput::make('transaction_amount')
                            ->prefix('$')
                            ->disabled()
                            ->label('Amount'),
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                    ])->columns(4),

                Forms\Components\Section::make('Detection Data')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('detection_rules')
                            ->disabled()
                            ->label('Triggered Rules'),
                        Forms\Components\KeyValue::make('detection_data')
                            ->disabled()
                            ->label('Detection Data'),
                    ])->columns(2),

                Forms\Components\Section::make('Review & Resolution')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(FraudAlert::STATUSES)
                            ->required(),
                        Forms\Components\Select::make('action_taken')
                            ->options(FraudAlert::ACTIONS)
                            ->nullable(),
                        Forms\Components\Textarea::make('review_notes')
                            ->label('Review Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alert_number')
                    ->label('Alert #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('Guest'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FraudAlert::TYPES[$state] ?? $state)
                    ->color('warning'),
                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'info',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('risk_score')
                    ->label('Risk')
                    ->suffix('/100')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 80 => 'danger',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'info',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('transaction_amount')
                    ->money()
                    ->label('Amount')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'confirmed_fraud' => 'danger',
                        'false_positive' => 'success',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('auto_blocked')
                    ->label('Blocked')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country_code')
                    ->label('Country')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Detected'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(FraudAlert::STATUSES)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('severity')
                    ->options(FraudAlert::SEVERITIES)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(FraudAlert::TYPES)
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('auto_blocked')
                    ->label('Auto Blocked'),
                Tables\Filters\Filter::make('high_risk')
                    ->label('High Risk (60+)')
                    ->query(fn (Builder $query): Builder => $query->where('risk_score', '>=', 60)),
                Tables\Filters\Filter::make('pending_review')
                    ->label('Pending Review')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('start_review')
                    ->label('Start Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn (FraudAlert $record) => $record->status === 'pending')
                    ->action(function (FraudAlert $record) {
                        $record->markAsReviewing();
                    }),
                Tables\Actions\Action::make('confirm_fraud')
                    ->label('Confirm Fraud')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (FraudAlert $record) => in_array($record->status, ['pending', 'reviewing']))
                    ->form([
                        Forms\Components\Select::make('action')
                            ->label('Action to Take')
                            ->options(FraudAlert::ACTIONS)
                            ->default('blocked'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->required(),
                    ])
                    ->action(function (FraudAlert $record, array $data) {
                        $record->confirmFraud($data['action'], $data['notes']);
                    }),
                Tables\Actions\Action::make('false_positive')
                    ->label('False Positive')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FraudAlert $record) => in_array($record->status, ['pending', 'reviewing']))
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes'),
                    ])
                    ->action(function (FraudAlert $record, array $data) {
                        $record->markAsFalsePositive($data['notes'] ?? null);
                    }),
                Tables\Actions\Action::make('block_ip')
                    ->label('Block IP')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (FraudAlert $record) => $record->ip_address !== null)
                    ->form([
                        Forms\Components\TextInput::make('hours')
                            ->label('Block Duration (hours)')
                            ->numeric()
                            ->placeholder('Leave empty for permanent'),
                        Forms\Components\TextInput::make('reason')
                            ->default(fn (FraudAlert $record) => 'Blocked due to fraud alert ' . $record->alert_number),
                    ])
                    ->action(function (FraudAlert $record, array $data) {
                        app(\App\Services\FraudDetectionService::class)->blockIp(
                            $record->ip_address,
                            $data['reason'],
                            $data['hours'] ? (int) $data['hours'] : null
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_false_positive')
                        ->label('Mark as False Positive')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->markAsFalsePositive('Bulk marked as false positive');
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Alert Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('alert_number')
                            ->label('Alert Number')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => FraudAlert::TYPES[$state] ?? $state),
                        Infolists\Components\TextEntry::make('severity')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'low' => 'info',
                                'medium' => 'warning',
                                'high' => 'danger',
                                'critical' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('risk_score')
                            ->label('Risk Score')
                            ->suffix('/100')
                            ->color(fn ($state): string => match (true) {
                                $state >= 80 => 'danger',
                                $state >= 60 => 'warning',
                                $state >= 40 => 'info',
                                default => 'success',
                            }),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'reviewing' => 'info',
                                'confirmed_fraud' => 'danger',
                                'false_positive' => 'success',
                                'resolved' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('auto_blocked')
                            ->label('Auto Blocked')
                            ->boolean(),
                    ])->columns(6),

                Infolists\Components\Section::make('Transaction Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User')
                            ->default('Guest'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('transaction_amount')
                            ->money()
                            ->label('Amount'),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Payment Method'),
                        Infolists\Components\TextEntry::make('payment_id')
                            ->label('Payment ID')
                            ->copyable(),
                    ])->columns(5),

                Infolists\Components\Section::make('Request Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('country_code')
                            ->label('Country'),
                        Infolists\Components\TextEntry::make('city')
                            ->label('City'),
                        Infolists\Components\TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->columnSpan(2),
                    ])->columns(5),

                Infolists\Components\Section::make('Detection Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('detection_rules')
                            ->label('Triggered Rules')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return 'None';
                                return collect($state)->map(fn ($rule) => ($rule['code'] ?? 'unknown') . ' (Score: ' . ($rule['score'] ?? 0) . ')')->join(', ');
                            })
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('detection_data')
                            ->label('Detection Data')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return 'None';
                                return json_encode($state, JSON_PRETTY_PRINT);
                            })
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Resolution')
                    ->schema([
                        Infolists\Components\TextEntry::make('reviewer.name')
                            ->label('Reviewed By'),
                        Infolists\Components\TextEntry::make('reviewed_at')
                            ->dateTime()
                            ->label('Reviewed At'),
                        Infolists\Components\TextEntry::make('action_taken')
                            ->label('Action Taken')
                            ->formatStateUsing(fn (?string $state): string => FraudAlert::ACTIONS[$state] ?? 'None'),
                        Infolists\Components\TextEntry::make('review_notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])->columns(3)
                    ->visible(fn (FraudAlert $record) => $record->reviewed_at !== null),

                Infolists\Components\Section::make('Timeline')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Detected At'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Last Updated'),
                    ])->columns(2),
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
            'index' => Pages\ListFraudAlerts::route('/'),
            'view' => Pages\ViewFraudAlert::route('/{record}'),
            'edit' => Pages\EditFraudAlert::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'reviewer']);
    }
}
