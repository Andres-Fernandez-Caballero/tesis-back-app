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

    public function getAllUsers(): JsonResponse
    {
        $users = $this->service->getAllUsers(10);
        return UserResource::collection($users)->response();
    }

    public function getAllTherapists(): JsonResponse
    {
        $therapists = $this->service->getAllTherapists(10);
        return UserResource::collection($therapists)->response();
    }

    public function getAllClients(): JsonResponse
    {
        $clients = $this->service->getAllClients(10);
        return UserResource::collection($clients)->response();
    }
    

    public function viewProfile(Request $request): JsonResponse
    {
        $user = $this->service->getProfile($request->user()->id);
        return response()->json(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        // ignorar el error si aparece es el editor
        $user = $this->service->updateProfile($request->user()->id, $request->validated());
        return response()->json(new UserResource($user));
    }

    
}
