<?php

namespace App\Filament\App\Resources\ReseniasResource\Pages;

use App\Filament\App\Resources\ReseniasResource;
use Filament\Resources\Pages\ListRecords;

class ListResenias extends ListRecords
{
    protected static string $resource = ReseniasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
