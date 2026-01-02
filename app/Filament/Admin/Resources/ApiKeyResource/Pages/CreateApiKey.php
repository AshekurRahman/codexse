<?php

namespace App\Filament\Admin\Resources\ApiKeyResource\Pages;

use App\Filament\Admin\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $key = ApiKey::generateKey();
        $secret = ApiKey::generateSecret();

        $data['key'] = $key;
        $data['secret_hash'] = Hash::make($secret);

        // Store secret temporarily for display
        session()->flash('api_secret', $secret);

        return $data;
    }

    protected function afterCreate(): void
    {
        $secret = session('api_secret');

        Notification::make()
            ->title('API Key Created')
            ->body("API Key: {$this->record->key}\n\nSecret: {$secret}\n\nPlease save the secret now - it won't be shown again!")
            ->warning()
            ->persistent()
            ->send();
    }
}
