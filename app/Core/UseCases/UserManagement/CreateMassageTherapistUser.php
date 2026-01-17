<?php

namespace App\Core\UseCases\UserManagement;

use App\Enums\Role;
use App\Events\RegisterTherapistProcessed;
use App\Models\User;
use App\Models\Users\UserData;

class CreateMassageTherapistUser
{


/**
 * @param array $data User data for creating a massage therapist user
 * @return User
 */
 public function execute(array $data)
 {
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        UserData::create([
            'user_id' => $user->id,
            'dni' => $data['dni'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'birth_date' => $data['birth_date'],
        ]);

        $user->assignRole(Role::MASSAGE_THERAPIST->value);
        event (new RegisterTherapistProcessed($user));

        return $user;
    }
}