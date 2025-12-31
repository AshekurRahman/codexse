<?php

namespace App\Filament\Admin\Resources\JobContractResource\Pages;

use App\Filament\Admin\Resources\JobContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobContract extends EditRecord
{
    protected static string $resource = JobContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
