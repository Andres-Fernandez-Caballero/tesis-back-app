<?php

namespace App\Models;

use App\Models\Therapists\Therapist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['local_id', 'nombre', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(
            Therapist::class,
            'especialidad_therapist',
            'especialidad_id',  // FK de este modelo en la pivot
            'therapist_id'      // FK del otro modelo en la pivot
        );
    }

    /**
     * Especialidades predefinidas que se crean automáticamente para cada local aprobado.
     */
    public static function defaults(): array
    {
        return [
            'Masaje descontracturante',
        ];
    }
}
