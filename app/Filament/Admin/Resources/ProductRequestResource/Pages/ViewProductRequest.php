<?php

namespace App\Filament\Admin\Resources\ProductRequestResource\Pages;

use App\Filament\Admin\Resources\ProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductRequest extends ViewRecord
{
    protected static string $resource = ProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
