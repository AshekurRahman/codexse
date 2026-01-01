<?php

namespace App\Filament\Admin\Resources\WalletTransactionResource\Pages;

use App\Filament\Admin\Resources\WalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWalletTransaction extends EditRecord
{
    protected static string $resource = WalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
