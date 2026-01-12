<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Therapists\Announcement;
use App\Models\Therapists\Therapist;
use App\Models\User;
use Database\Seeders\Therapists\TagSeeder;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

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


        // Crea admin de pruebas si no existe
        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::factory()->withUserData()->create([
                'name' => 'Administrador',
                'email' => 'admin@gmail.com',
            ])->assignRole([Role::ADMIN]);
        }

        // crea cliente de pruebas si no existe
        if (!User::where('email', 'client@gmail.com')->exists()) {
            User::factory()->withUserData()->create([
                'name' => 'Cliente',
                'email' => 'client@gmail.com',
            ])->assignRole([Role::CLIENT]);
        }

        User::factory(10)->withUserData()->create()
            ->each(
                fn(User $user) => $user->assignRole([Role::CLIENT])
            );


        

        Therapist::factory(10)->massageTherapist()->create()
            ->each(
                function (Therapist $therapist) {
                    Announcement::factory()->create([
                        'therapist_id' => $therapist->id,
                    ])->each(
                        function (Announcement $announcement) {
                            $tag = Tag::all()->random()->first();
                            $announcement->dicipline = $tag->name;
                            //$announcement->dicipline = "Descontracturante";
                        }
                    );
                }
            );
    }
}
