<?php

namespace App\Filament\Admin\Resources\AffiliateResource\Pages;

use App\Filament\Admin\Resources\AffiliateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliate extends EditRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
