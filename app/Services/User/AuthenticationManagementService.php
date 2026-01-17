<?php

namespace App\Services\User;

use App\Core\UseCases\UserManagement\CreateClientUser;
use App\Core\UseCases\UserManagement\CreateMassageTherapistUser;
use App\Enums\Role;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationManagementService 
{
    public function __construct(
        protected readonly UserRepository $userRepository,
        protected readonly CreateClientUser $createClientUser,
        protected readonly CreateMassageTherapistUser $createMassageTherapistUser
        ) {}

    public function registerUser(array $data, Role $role = Role::CLIENT)
    {
        $data['password'] = Hash::make($data['password']);

        if($role == Role::CLIENT) {
            $newUser = $this->createClientUser->execute($data);
        }elseif ($role == Role::MASSAGE_THERAPIST) {
            $newUser= $this->createMassageTherapistUser->execute($data);
        }else {
            throw new \Exception('Role Not Found');
        }
        
        if (!$newUser) {
            throw new \Exception('User registration failed');
        }

        $newUser->assignRole($role);

        Auth::login($newUser);
        return Auth::user()->createToken('auth_token')->plainTextToken;
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return Auth::user()->createToken('auth_token')->plainTextToken;
    }

    public function sendResetPasswordLink(array $data)
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user) {
            abort(404, 'User not found');
        }

        // Logic to send reset password link
        return true;
    }
}