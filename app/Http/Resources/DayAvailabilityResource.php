<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DayAvailabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date'        => $this->date,
            'dayOfWeek' => $this->dayOfWeek,
            'available'   => $this->available,
            'reason'      => $this->reason,
            'slots'       => SlotResource::collection($this->slots),
        ];
    }
}
