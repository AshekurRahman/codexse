<?php

namespace App\Filament\Admin\Resources\FraudAlertResource\Pages;

use App\Filament\Admin\Resources\FraudAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFraudAlert extends EditRecord
{
    protected static string $resource = FraudAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If status changed to a resolved state, set reviewed info
        if (in_array($data['status'], ['confirmed_fraud', 'false_positive', 'resolved'])) {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }

        return $data;
    }
}
