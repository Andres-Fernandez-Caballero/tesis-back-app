<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TherapistResource extends JsonResource
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
            // Los masajistas gestionados por un local no tienen cuenta de usuario propia.
            'name' => $this->user
                ? trim("{$this->user->name} {$this->user->last_name}")
                : $this->nombre,
            'specialization' => $this->specialization,
            'score' => $this->user?->score,
            'state' => $this->user ? UserStateResource::make($this->user->state) : null,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
