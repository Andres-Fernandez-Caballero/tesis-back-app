<?php

namespace App\Filament\Admin\Resources\LocalRegistrationResource\Pages;

use App\Filament\Admin\Resources\LocalRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLocalRegistration extends ViewRecord
{
    protected static string $resource = LocalRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
