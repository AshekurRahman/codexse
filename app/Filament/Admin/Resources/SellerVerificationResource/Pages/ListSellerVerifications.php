<?php

namespace App\Filament\Admin\Resources\SellerVerificationResource\Pages;

use App\Filament\Admin\Resources\SellerVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellerVerifications extends ListRecords
{
    protected static string $resource = SellerVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
