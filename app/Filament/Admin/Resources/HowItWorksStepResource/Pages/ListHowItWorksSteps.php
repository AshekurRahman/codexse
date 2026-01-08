<?php

namespace App\Filament\Admin\Resources\HowItWorksStepResource\Pages;

use App\Filament\Admin\Resources\HowItWorksStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHowItWorksSteps extends ListRecords
{
    protected static string $resource = HowItWorksStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
