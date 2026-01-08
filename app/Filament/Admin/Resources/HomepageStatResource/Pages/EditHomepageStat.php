<?php

namespace App\Filament\Admin\Resources\HomepageStatResource\Pages;

use App\Filament\Admin\Resources\HomepageStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomepageStat extends EditRecord
{
    protected static string $resource = HomepageStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
