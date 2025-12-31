<?php

namespace App\Filament\Admin\Resources\ServiceOrderResource\Pages;

use App\Filament\Admin\Resources\ServiceOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceOrder extends CreateRecord
{
    protected static string $resource = ServiceOrderResource::class;
}
