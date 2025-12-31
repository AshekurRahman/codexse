<?php

namespace App\Filament\Admin\Resources\DisputeResource\Pages;

use App\Filament\Admin\Resources\DisputeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisputes extends ListRecords
{
    protected static string $resource = DisputeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
