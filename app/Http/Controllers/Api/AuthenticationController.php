<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Therapists\StoreTherapistRequest;
use App\Http\Requests\Users\ForgotPasswordRequest;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterUserRequest;
use App\Models\Therapists\FactoryTherapist;
use App\Services\AuthenticationManagementService;
use App\Services\TherapistManagementService;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller 
{

    public function __construct(
        protected readonly AuthenticationManagementService $service) {}
 /**
     * Register a new user type client or therapist
     * @param \App\Http\Requests\Users\RegisterUserRequest $request
     * @return JsonResponse|mixed
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->service->registerUser($request->validated());
        return response()->json(new UserResource($user), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try{
            $token = $this->service->login($request->validated());
            return response()->json(['token' => $token]);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendResetPasswordLink($request->validated());
        return response()->json(['message' => 'Password reset link sent']);
    }
}
