<?php

namespace App\Filament\Admin\Resources\SubscriptionPlanResource\Pages;

use App\Filament\Admin\Resources\SubscriptionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionPlan extends ViewRecord
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
