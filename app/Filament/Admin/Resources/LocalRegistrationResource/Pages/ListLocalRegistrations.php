<?php

namespace App\Filament\Admin\Resources\LocalRegistrationResource\Pages;

use App\Filament\Admin\Resources\LocalRegistrationResource;
use Filament\Resources\Pages\ListRecords;

class ListLocalRegistrations extends ListRecords
{
    protected static string $resource = LocalRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
