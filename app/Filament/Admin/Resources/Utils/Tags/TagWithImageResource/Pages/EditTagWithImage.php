<?php

namespace App\Filament\Admin\Resources\Utils\Tags\TagWithImageResource\Pages;

use App\Filament\Admin\Resources\Utils\Tags\TagWithImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagWithImage extends EditRecord
{
    protected static string $resource = TagWithImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
