<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\Therapists\AnnouncementFactory> */
    use HasFactory;
    use HasTags; 

    protected $guarded = [];

    protected static function booted(){
        static::saved(function ($announcement) {
            // por seguridad de que no se agreguen más de una categoria
            if ($announcement->tags()->count() > 1) {
                $announcement->syncTags([$announcement->tags()->first()]);
            }
        });
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    /**
     * Getter personalizado para obtener las disciplinas como array de strings.
     *
     * @return Tag
     */
    public function getDiciplineAttribute(): Tag
    {
        return $this->tagsWithType('dicipline')->first();
    }

    /**
     * Setter personalizado para asignar las disciplinas (tags de tipo 'dicipline').
     *
     * @param array|string $diciplines
     * @return void
     */
    public function setDiciplineAttribute(string $dicipline): void
    {
        // Normalizar entrada (puede ser string o array)
        $diciplines = is_array($dicipline) ? $dicipline : [$dicipline];

        // Sincroniza los tags del tipo 'dicipline'
        $this->syncTagsWithType($diciplines, 'dicipline');
    }
}
