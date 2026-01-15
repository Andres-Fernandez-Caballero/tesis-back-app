<?php

namespace App\Filament\App\Resources\TherapistAnnouncementsResource\Pages;

use App\Filament\App\Resources\TherapistAnnouncementsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateTherapistAnnouncements extends CreateRecord
{
    protected static string $resource = TherapistAnnouncementsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['therapist_id'] = auth()->user()->therapist->id;
        return $data;
    }


    protected function afterSave(): void
    {
        $dicipline = $this->data['new_dicipline'] ?? null;

        if ($dicipline) {
            $this->record->dicipline = $dicipline;
        }
    }
}
