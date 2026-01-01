<?php

namespace App\Filament\Admin\Resources\TaxRateResource\Pages;

use App\Filament\Admin\Resources\TaxRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxRates extends ListRecords
{
    protected static string $resource = TaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
