<?php

namespace Database\Factories\Therapists;

use App\Models\Therapists\Therapist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Therapists\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scoring' => $this->faker->numberBetween(0, 1000),
            'is_active' => $this->faker->boolean(),
            'title' => $this->faker->sentence(),
            'description' => 'lorem ipsum dolor sit amet.',
            'price' => $this->faker->randomFloat(2, 1000, 9000),
            'durationInMinutes' => $this->faker->randomElement([30, 45, 60, 90]),
            'therapist_id' => Therapist::factory(), // Assuming you have a TherapistFactory
        ];
    }
}
