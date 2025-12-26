<?php

namespace App\Filament\Admin\Resources\AiChatSessionResource\Pages;

use App\Filament\Admin\Resources\AiChatSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiChatSessions extends ListRecords
{
    protected static string $resource = AiChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
