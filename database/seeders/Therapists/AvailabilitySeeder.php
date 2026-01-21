<?php

namespace Database\Seeders\Therapists;

use App\Models\Therapists\Availability;
use App\Models\Therapists\Therapist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $therapists = Therapist::all();

        foreach ($therapists as $therapist) {
            for ($day = 1; $day <= 5; $day++) {
                Availability::firstOrCreate(
                    [
                        'therapist_id' => $therapist->id,
                        'day_of_week'  => $day,
                        'start_time'   => '09:00',
                        'end_time'     => '21:00',
                    ],
                    []
                );
            }
        }
    }
}
