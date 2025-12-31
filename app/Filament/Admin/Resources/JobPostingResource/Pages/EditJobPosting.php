<?php

namespace App\Filament\Admin\Resources\JobPostingResource\Pages;

use App\Filament\Admin\Resources\JobPostingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobPosting extends EditRecord
{
    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
