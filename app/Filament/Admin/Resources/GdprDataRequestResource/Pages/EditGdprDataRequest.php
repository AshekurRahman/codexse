<?php

namespace App\Filament\Admin\Resources\GdprDataRequestResource\Pages;

use App\Filament\Admin\Resources\GdprDataRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGdprDataRequest extends EditRecord
{
    protected static string $resource = GdprDataRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
