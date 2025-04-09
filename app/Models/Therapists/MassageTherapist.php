<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassageTherapist extends Therapist
{
    /** @use HasFactory<\Database\Factories\Therapists\MassageTherapistFactory> */
    use HasFactory;
    protected $table = 'therapists';
    protected $visible = ['field_m'];
}
