<?php

namespace App\Filament\Admin\Resources\LiveChatResource\Pages;

use App\Filament\Admin\Resources\LiveChatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLiveChats extends ListRecords
{
    protected static string $resource = LiveChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('live_support')
                ->label('Open Live Support')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->url(fn () => \App\Filament\Admin\Pages\LiveChatSupport::getUrl())
                ->color('primary'),
        ];
    }
}
