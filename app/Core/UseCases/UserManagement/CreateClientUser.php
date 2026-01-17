<?php

namespace App\Core\UseCases\UserManagement;

use App\Enums\Role;
use App\Events\RegisterClientProcessed;
use App\Models\User;
use App\Models\Users\UserData;

class CreateClientUser {

    /**
    * @param array $data User data for creating a client user
    * @return User
    */
    public function execute(array $data) {
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'password' => $data['password']
        ]);

        UserData::create([
        'user_id' => $user->id,
        'phone' => $data['phone'],
        'birth_date' => $data['birth_date'],
        'gender' => $data['gender'],
        ]);

        $user->assignRole(Role::CLIENT->value);
        event(new RegisterClientProcessed($user));

        return $user;
    }
}