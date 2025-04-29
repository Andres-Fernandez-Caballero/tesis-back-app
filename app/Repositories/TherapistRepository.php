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

    public function getAllMassageTherapists(string $type)
    {
        if($type || !empty($type)){
            return Therapist::where('type', $type)->get();
        }

        return Therapist::all();
    }
}