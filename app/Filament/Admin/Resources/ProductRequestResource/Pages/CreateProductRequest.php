<?php

namespace App\Filament\Admin\Resources\ProductRequestResource\Pages;

use App\Filament\Admin\Resources\ProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductRequest extends CreateRecord
{
    protected static string $resource = ProductRequestResource::class;
}
