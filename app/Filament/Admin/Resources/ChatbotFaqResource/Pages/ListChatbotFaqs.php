<?php

namespace App\Filament\Admin\Resources\ChatbotFaqResource\Pages;

use App\Filament\Admin\Resources\ChatbotFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbotFaqs extends ListRecords
{
    protected static string $resource = ChatbotFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
