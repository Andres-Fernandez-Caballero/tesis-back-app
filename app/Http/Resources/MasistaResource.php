<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MasistaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nombre'        => $this->nombre,
            'descripcion'   => $this->descripcion,
            'fotoUrl'       => $this->foto_url
                                ? Storage::disk('public')->url($this->foto_url)
                                : null,
            'especialidades' => $this->whenLoaded(
                'especialidades',
                fn () => $this->especialidades->pluck('nombre')
            ),
        ];
    }
}
