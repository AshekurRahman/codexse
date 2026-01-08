<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $originalData = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalData = $data;
        return $data;
    }

    protected function afterSave(): void
    {
        $changes = [
            'old' => array_intersect_key($this->originalData, $this->data),
            'new' => $this->data,
        ];

        UserResource::logUpdated($this->record, $changes);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    UserResource::logDeleted($this->record);
                }),
        ];
    }
}
