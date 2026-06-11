<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Enums\Role;
use App\Filament\Admin\Resources\UserResource;
use App\Models\Users\UserData;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Asignar rol cliente
        $this->record->assignRole(Role::CLIENT->value);

        // Crear registro vacío en user_data
        UserData::firstOrCreate(['user_id' => $this->record->id]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
