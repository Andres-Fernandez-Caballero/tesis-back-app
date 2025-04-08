<?php

namespace Database\Factories\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Users\UserData>
 */
class UserDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'dni' => $this->faker->unique()->numerify('########'),
            'birth_date' => $this->faker->date(),
            'phone' => $this->faker->unique()->numerify('########'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address(),
            'user_id' => User::factory(), // Relación con User
        ];
    }
}
