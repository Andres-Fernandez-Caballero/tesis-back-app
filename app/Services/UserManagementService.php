<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile(int $userId)
    {
        return $this->userRepository->findById($userId);
    }

    public function updateProfile(int $userId, array $data)
    {
        return $this->userRepository->update($userId, $data);
    }

    public function deleteProfile(int $userId)
    {
        return $this->userRepository->delete($userId);
    }

    public function registerUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            abort(401, 'Invalid Credentials');
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

    public function unBanUsers()
    {
        //
    }
}