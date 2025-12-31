<?php

namespace App\Filament\Admin\Resources\EscrowTransactionResource\Pages;

use App\Filament\Admin\Resources\EscrowTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEscrowTransaction extends CreateRecord
{
    protected static string $resource = EscrowTransactionResource::class;
}
