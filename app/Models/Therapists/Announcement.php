<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\Therapists\AnnouncementFactory> */
    use HasFactory;
    use HasTags;

    protected $guarded = [];

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    /**
     * Getter personalizado para obtener las disciplinas como array de strings.
     *
     * @return array
     */
    public function getDiciplinesAttribute(): array
    {
        return $this->tagsWithType('dicipline')->pluck('name')->toArray();
    }

    /**
     * Setter personalizado para asignar las disciplinas (tags de tipo 'dicipline').
     *
     * @param array|string $diciplines
     * @return void
     */
    public function setDiciplinesAttribute(array|string $diciplines): void
    {
        // Normalizar entrada (puede ser string o array)
        $diciplines = is_array($diciplines) ? $diciplines : [$diciplines];

        // Sincroniza los tags del tipo 'dicipline'
        $this->syncTagsWithType($diciplines, 'dicipline');
    }
}
