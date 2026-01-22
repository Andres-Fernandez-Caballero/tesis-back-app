<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'therapistId' => $this->therapist_id,
            'date' => $this->date,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'notes' => $this->notes,
            'state' => [
                'name' => $this->resource->state,
                'label' => $this->resource->state->label(),
                'description' => $this->resource->state->description(),
            ],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
