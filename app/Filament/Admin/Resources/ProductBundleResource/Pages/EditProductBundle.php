<?php

namespace App\Filament\Admin\Resources\ProductBundleResource\Pages;

use App\Filament\Admin\Resources\ProductBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductBundle extends EditRecord
{
    protected static string $resource = ProductBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
