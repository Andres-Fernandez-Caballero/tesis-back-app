<?php

namespace App\Services\User;

use App\Enums\Role;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationManagementService 
{
    public function __construct(protected readonly UserRepository $userRepository) {}

    public function registerUser(array $data, Role $role = Role::CLIENT)
    {
        $data['password'] = Hash::make($data['password']);
        
        $newUser = $this->userRepository->create($data);

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