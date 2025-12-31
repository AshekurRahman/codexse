<?php

namespace App\Filament\Admin\Resources\EmailCampaignResource\Pages;

use App\Filament\Admin\Resources\EmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailCampaign extends EditRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
