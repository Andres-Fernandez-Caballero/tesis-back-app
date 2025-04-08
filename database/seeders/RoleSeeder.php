<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(PermissionEnum::values() as $permission){
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach(RoleEnum::values() as $role){
            Role::firstOrCreate(['name' => $role]);
        }

        // asigo permisos a los roles
        Role::findByName(RoleEnum::ADMIN->value)->syncPermissions([
            PermissionEnum::ADMINISTRATE_BANNED_USERS->value,   
        ]);
    }
}
