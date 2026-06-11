<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TherapistResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'id'             => $this->id,
            'therapistId'    => $this->therapist_id,
            'announcementId' => $this->announcement_id,
            'localId'        => $this->local_id,
            'especialidadId' => $this->especialidad_id,
            'date'           => $this->date,
            'startTime'      => $this->start_time,
            'endTime'        => $this->end_time,
            'price'          => $this->price,
            'notes'          => $this->notes,
            'state'          => [
                'name'        => $this->resource->getRawOriginal('state'),
                'label'       => $this->resource->state->label(),
                'description' => $this->resource->state->description(),
            ],
            'hasReview'          => $this->whenLoaded('review', fn () => $this->review !== null, false),
            'reviewLocalScore'   => $this->whenLoaded('review', fn () => $this->review?->local_score),
            'localName'          => $this->whenLoaded('local', fn () => $this->local?->nombre_local),
            'localDireccion'     => $this->whenLoaded('local', fn () => $this->local?->direccion),
            'localLocalidad'     => $this->whenLoaded('local', fn () => $this->local?->localidad),
            'especialidadNombre' => $this->whenLoaded('especialidad', fn () => $this->especialidad?->nombre),
            'therapistName'      => $this->whenLoaded('therapist', fn () => $this->therapist?->user?->name),
            'therapist'      => $this->whenLoaded(
                'therapist',
                fn () => TherapistResource::make($this->therapist)
            ),
            'announcement'   => $this->whenLoaded(
                'annuncement',
                fn () => [
                    'id'    => $this->annuncement->id,
                    'title' => $this->annuncement->title,
                    'price' => $this->annuncement->price,
                ]
            ),
            'createdAt'      => $this->created_at,
            'updatedAt'      => $this->updated_at,
        ];
    }
}
