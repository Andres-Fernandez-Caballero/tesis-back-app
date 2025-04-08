<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Models\Users\UserData;
use Database\Seeders\Therapists\TagSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TagSeeder::class,
            RoleSeeder::class,
        ]);



        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::factory()->withUserData()->create([
                'name' => 'Administrador',
                'email' => 'admin@gmail.com',
            ])->assignRole([Role::ADMIN]);
        }
        User::factory(50)->withUserData()->create()
            ->each(
                fn(User $user) => $user->assignRole([Role::CLIENT])
            );
        User::factory(10)->withUserData()->create()
            ->each(
                fn(User $user) => $user->assignRole([Role::MASSAGE_THERAPIST])
            );
    }
}
