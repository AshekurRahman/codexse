<?php

namespace App\Filament\Admin\Resources\EscrowTransactionResource\Pages;

use App\Filament\Admin\Resources\EscrowTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEscrowTransaction extends EditRecord
{
    protected static string $resource = EscrowTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
