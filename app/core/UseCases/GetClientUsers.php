<?php

namespace App\Core\UseCases;

use App\Enums\Role;
use App\Models\User;

class GetClientUsers 
{
    public function execute()
    {
        User::role(Role::ADMIN)->get();
    }
}