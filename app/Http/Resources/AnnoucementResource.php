<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnoucementResource extends JsonResource
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
            'scoring' => $this->scoring,
            'title' => $this->title,
            'content' => $this->content ?? "",
            'duration' => $this->duration_in_minutes,
            'currency' => $this->currency,
            'therapist' => TherapistResource::make($this->therapist),
            'price' => $this->price,
            'dicipline' => TagResource::make($this->dicipline),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
