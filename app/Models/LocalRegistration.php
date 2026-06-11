<?php

namespace App\Models;

use App\Enums\LocalRegistrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LocalRegistration extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'nombre_local',
        'direccion',
        'cuit',
        'instagram',
        'email',
        'telefono',
        'descripcion',
        'localidad',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => LocalRegistrationStatus::class,
        ];
    }

    public function local(): HasOne
    {
        return $this->hasOne(Local::class);
    }

    public function isPending(): bool
    {
        return $this->status === LocalRegistrationStatus::PENDING;
    }
}
