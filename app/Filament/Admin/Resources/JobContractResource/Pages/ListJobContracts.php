<?php

namespace App\Filament\Admin\Resources\JobContractResource\Pages;

use App\Filament\Admin\Resources\JobContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobContracts extends ListRecords
{
    protected static string $resource = JobContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
