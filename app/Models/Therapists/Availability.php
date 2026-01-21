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

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}

