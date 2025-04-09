<?php

namespace App\Repositories;

use App\Models\Therapists\Therapist;

class TherapistRepository
{
    public function create(Therapist $therapist){
        $therapist->save();
        return $therapist;
    }

    public function getAll()
    {
        return Therapist::all();
    }

    public function getAllMassageTherapists()
    {
        return Therapist::where('type', 'MassageTherapist')->get();
    }
}