<?php

namespace App\Models\Users\Traits;

use App\Enums\Role;
use App\Models\Therapists\Therapist;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTherapist
{
    public function therapist(): HasOne
    {
        return $this->belongsTo(Therapist::class);
    }

    public function isTherapist(): bool
    {
        return $this->roles->whereIn([
            Role::MASSAGE_THERAPIST,
        ]);
    }
}