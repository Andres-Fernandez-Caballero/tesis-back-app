<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LocalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nombre'              => $this->nombre_local,
            'direccion'           => $this->direccion,
            'telefono'            => $this->telefono,
            'email'               => $this->email,
            'descripcion'         => $this->descripcion,
            'instagram'           => $this->instagram,
            'localidad'           => $this->localidad,
            'latitude'            => $this->latitude ? (float) $this->latitude : null,
            'longitude'           => $this->longitude ? (float) $this->longitude : null,
            'imageUrl'            => $this->image ? Storage::disk('public')->url($this->image) : null,
            'slotDurationMinutes' => $this->slot_duration_minutes,
            'avgLocalScore'       => $this->reviews_avg_local_score !== null
                                        ? round((float) $this->reviews_avg_local_score, 2)
                                        : null,
            'reviewsCount'        => (int) ($this->reviews_count ?? 0),
        ];
    }
}
