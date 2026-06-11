<?php

namespace App\Filament\Admin\Resources\LocalesAdminResource\Pages;

use App\Filament\Admin\Resources\LocalesAdminResource;
use Filament\Resources\Pages\ListRecords;

class ListLocalesAdmin extends ListRecords
{
    protected static string $resource = LocalesAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
