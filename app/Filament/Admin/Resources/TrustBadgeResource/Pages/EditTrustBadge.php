<?php

namespace App\Filament\Admin\Resources\TrustBadgeResource\Pages;

use App\Filament\Admin\Resources\TrustBadgeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrustBadge extends EditRecord
{
    protected static string $resource = TrustBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
