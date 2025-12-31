<?php

namespace App\Filament\Admin\Resources\LiveChatResource\Pages;

use App\Filament\Admin\Resources\LiveChatResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;

class ViewLiveChat extends ViewRecord
{
    protected static string $resource = LiveChatResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Chat Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('visitor_name')
                                    ->label('Visitor'),

                                TextEntry::make('visitor_email')
                                    ->label('Email')
                                    ->copyable(),

                                TextEntry::make('department')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => \App\Models\LiveChat::DEPARTMENTS[$state] ?? $state),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'waiting' => 'warning',
                                        'active' => 'success',
                                        'closed' => 'gray',
                                        default => 'gray',
                                    }),

                                TextEntry::make('agent.name')
                                    ->label('Agent')
                                    ->placeholder('Unassigned'),

                                TextEntry::make('subject')
                                    ->placeholder('No subject'),

                                TextEntry::make('created_at')
                                    ->dateTime(),

                                TextEntry::make('started_at')
                                    ->dateTime()
                                    ->placeholder('Not started'),

                                TextEntry::make('ended_at')
                                    ->dateTime()
                                    ->placeholder('Active'),

                                TextEntry::make('rating')
                                    ->formatStateUsing(fn (?int $state): string => $state ? str_repeat('★', $state) . str_repeat('☆', 5 - $state) : 'No rating')
                                    ->color('warning'),

                                TextEntry::make('feedback')
                                    ->columnSpan(2)
                                    ->placeholder('No feedback'),
                            ]),
                    ]),

                Section::make('Chat Transcript')
                    ->schema([
                        RepeatableEntry::make('messages')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        TextEntry::make('sender_type')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'visitor' => 'info',
                                                'agent' => 'success',
                                                'system' => 'gray',
                                                default => 'gray',
                                            })
                                            ->columnSpan(2),

                                        TextEntry::make('message')
                                            ->columnSpan(8),

                                        TextEntry::make('created_at')
                                            ->dateTime('H:i')
                                            ->columnSpan(2),
                                    ]),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
