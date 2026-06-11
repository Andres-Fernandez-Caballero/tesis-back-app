<?php

namespace App\Filament\App\Resources\DisponibilidadResource\Pages;

use App\Filament\App\Resources\DisponibilidadResource;
use App\Models\Therapists\Availability;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditDisponibilidad extends EditRecord
{
    protected static string $resource = DisponibilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $data = $this->data;
        $days = (array) $data['day_of_week'];
        $diasOptions = DisponibilidadResource::diasOptions();

        foreach ($days as $day) {
            $hasOverlap = Availability::where('therapist_id', $data['therapist_id'])
                ->whereJsonContains('day_of_week', (int) $day)
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->where('id', '!=', $this->record->id)
                ->exists();

            if ($hasOverlap) {
                $dayName = $diasOptions[$day] ?? $day;
                throw ValidationException::withMessages([
                    'data.day_of_week' => "El masajista ya tiene una franja que se superpone el {$dayName}.",
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
