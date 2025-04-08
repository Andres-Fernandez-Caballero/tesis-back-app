<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\Users\RegisterUserRequest;
use App\Http\Requests\Users\ForgotPasswordRequest;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected UserManagementService $service;

    public function __construct(UserManagementService $service)
    {
        $this->service = $service;
    }

    public function viewProfile(Request $request): JsonResponse
    {
        $user = $this->service->getProfile($request->user()->id);
        return response()->json(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->updateProfile($request->user()->id, $request->validated());
        return response()->json(new UserResource($user));
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->service->registerUser($request->validated());
        return response()->json(new UserResource($user), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->service->login($request->validated());
        return response()->json(['token' => $token]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendResetPasswordLink($request->validated());
        return response()->json(['message' => 'Password reset link sent']);
    }
}
