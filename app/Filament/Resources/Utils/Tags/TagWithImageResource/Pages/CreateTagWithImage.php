<?php

namespace App\Filament\Resources\Utils\Tags\TagWithImageResource\Pages;

use App\Filament\Resources\Utils\Tags\TagWithImageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTagWithImage extends CreateRecord
{
    protected static string $resource = TagWithImageResource::class;
}
