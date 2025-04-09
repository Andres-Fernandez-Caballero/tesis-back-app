<?php

namespace App\Models\Therapists;

class FactoryTherapist
{
    public static function make(array $data): Therapist
    {
        $type = $data["type"] ?? null;

        return match($type){
            'MassageTherapist' => new MassageTherapist($data),
            'OtherTherapist' => new OtherTherapist($data),
            default => throw new \InvalidArgumentException("Invalid therapist type: $type"),
        };
    }
}
