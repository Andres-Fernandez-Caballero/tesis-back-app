<?php

namespace Database\Factories\Therapists;

use App\Enums\Role;
use App\Models\Therapists\Therapist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Therapists\Therapist>
 */
class TherapistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'certificate_file' => $this->faker->filePath(),
            'type' => 'MassageTherapist',
            'certificate_file_name' => 'certificado_masajista',
            'certificate_file_create_date' => $this->faker->date(),
            'certificate_file_expiration_date' => $this->faker->date(),
            'user_id' => User::factory()->withUserData(),
        ];
    }
    
    public function massageTherapist(): static
    {
        return $this->state([
            'type' => 'MassageTherapist',
            'user_id' => User::factory()->withUserData(),
        ])->afterCreating(function (Therapist $therapist) {
            $therapist->user->assignRole(Role::MASSAGE_THERAPIST);
        });
    }
}
