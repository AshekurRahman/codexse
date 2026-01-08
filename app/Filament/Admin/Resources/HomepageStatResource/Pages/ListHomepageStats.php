<?php

namespace App\Filament\Admin\Resources\HomepageStatResource\Pages;

use App\Filament\Admin\Resources\HomepageStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepageStats extends ListRecords
{
    protected static string $resource = HomepageStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
