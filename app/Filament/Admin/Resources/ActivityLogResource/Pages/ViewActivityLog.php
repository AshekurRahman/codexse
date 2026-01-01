<?php

namespace App\Filament\Admin\Resources\ActivityLogResource\Pages;

use App\Filament\Admin\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Activity Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User')
                                    ->default('Guest'),
                                TextEntry::make('action')
                                    ->badge()
                                    ->color(fn (ActivityLog $record): string => $record->action_color)
                                    ->formatStateUsing(fn (string $state): string => ActivityLog::ACTIONS[$state] ?? ucfirst(str_replace('_', ' ', $state))),
                                TextEntry::make('category')
                                    ->badge()
                                    ->color('gray')
                                    ->formatStateUsing(fn (string $state): string => ActivityLog::CATEGORIES[$state] ?? ucfirst($state)),
                            ]),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Time')
                            ->dateTime('F j, Y g:i:s A'),
                    ]),

                Section::make('Subject')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('subject_type')
                                    ->label('Subject Type')
                                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                                TextEntry::make('subject_id')
                                    ->label('Subject ID')
                                    ->default('-'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Changes')
                    ->schema([
                        TextEntry::make('old_values')
                            ->label('Old Values')
                            ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-')
                            ->prose(),
                        TextEntry::make('new_values')
                            ->label('New Values')
                            ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-')
                            ->prose(),
                    ])
                    ->columns(2)
                    ->visible(fn (ActivityLog $record) => $record->old_values || $record->new_values)
                    ->collapsible(),

                Section::make('Additional Properties')
                    ->schema([
                        TextEntry::make('properties')
                            ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-')
                            ->prose(),
                    ])
                    ->visible(fn (ActivityLog $record) => !empty($record->properties))
                    ->collapsible(),

                Section::make('Request Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->copyable(),
                                TextEntry::make('device_type')
                                    ->label('Device Type')
                                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Unknown')),
                                TextEntry::make('browser')
                                    ->default('Unknown'),
                                TextEntry::make('platform')
                                    ->label('OS')
                                    ->default('Unknown'),
                            ]),
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull()
                            ->limit(100),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country_code')
                                    ->label('Country')
                                    ->default('-'),
                                TextEntry::make('city')
                                    ->default('-'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Security')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('risk_level')
                                    ->label('Risk Level')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        default => 'success',
                                    }),
                                IconEntry::make('is_suspicious')
                                    ->label('Suspicious')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-exclamation-triangle')
                                    ->falseIcon('heroicon-o-check-circle')
                                    ->trueColor('danger')
                                    ->falseColor('success'),
                                TextEntry::make('causer_type')
                                    ->label('Causer Type')
                                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Unknown')),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
