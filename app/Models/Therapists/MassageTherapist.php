<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MassageTherapist extends Therapist
{
    protected $table = 'therapists';
    // protected $visible = ['field_m'];
}
