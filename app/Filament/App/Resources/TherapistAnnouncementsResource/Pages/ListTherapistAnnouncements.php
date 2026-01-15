<?php

namespace App\Filament\App\Resources\TherapistAnnouncementsResource\Pages;

use App\Filament\App\Resources\TherapistAnnouncementsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTherapistAnnouncements extends ListRecords
{
    protected static string $resource = TherapistAnnouncementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
