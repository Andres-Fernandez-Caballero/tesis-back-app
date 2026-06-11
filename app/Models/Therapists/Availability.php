<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    /** @use HasFactory<\Database\Factories\Therapists\AvailabilityFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'day_of_week' => 'array',
    ];

    public function setDayOfWeekAttribute(mixed $value): void
    {
        // Normalize to an array of integers regardless of input type
        $days = is_array($value) ? $value : json_decode($value ?? '[]', true);
        $this->attributes['day_of_week'] = json_encode(
            array_values(array_map('intval', (array) $days))
        );
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}

