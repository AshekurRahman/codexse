<?php

namespace App\Filament\Admin\Resources\TrustBadgeResource\Pages;

use App\Filament\Admin\Resources\TrustBadgeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrustBadges extends ListRecords
{
    protected static string $resource = TrustBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
