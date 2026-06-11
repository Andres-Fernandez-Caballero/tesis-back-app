<?php

namespace App\Models;

use App\Enums\LocalStatus;
use App\Models\Especialidad;
use App\Models\Review;
use App\Models\Therapists\Therapist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locals';

    protected $fillable = [
        'nombre_local',
        'direccion',
        'cuit',
        'telefono',
        'email',
        'descripcion',
        'localidad',
        'latitude',
        'longitude',
        'image',
        'instagram',
        'status',
        'slot_duration_minutes',
        'local_registration_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => LocalStatus::class,
        ];
    }

    public function localRegistration(): BelongsTo
    {
        return $this->belongsTo(LocalRegistration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapists(): HasMany
    {
        return $this->hasMany(Therapist::class);
    }

    public function especialidades(): HasMany
    {
        return $this->hasMany(Especialidad::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function seedDefaultEspecialidades(): void
    {
        foreach (Especialidad::defaults() as $nombre) {
            $this->especialidades()->firstOrCreate(['nombre' => $nombre]);
        }
    }

    public function isActive(): bool
    {
        return $this->status === LocalStatus::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === LocalStatus::SUSPENDED;
    }
}
