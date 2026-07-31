<?php

namespace Database\Seeders;

use App\Models\Subscriptions\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'Plan Base'],
            ['price' => 18000, 'currency' => 'ARS', 'is_active' => true],
        );
    }
}
