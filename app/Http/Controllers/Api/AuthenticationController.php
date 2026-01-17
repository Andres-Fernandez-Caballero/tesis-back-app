<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ForgotPasswordRequest;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterUserClientRequest;
use App\Http\Requests\Users\RegisterUserTherapistRequest;
use App\Http\Resources\UserLoguedResource;
use App\Services\User\AuthenticationManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{

    public function __construct(
        protected readonly AuthenticationManagementService $service
    ) {}
    
    /**
     * Register a new user type client or therapist
     * @param \App\Http\Requests\Users\RegisterUserClientRequest $request
     * @return JsonResponse|mixed
     */
    public function registerClient(RegisterUserClientRequest $request): JsonResponse
    {
        $token = $this->service->registerUser($request->validated(), Role::CLIENT);
        $dataUser = clone Auth::user();
        $dataUser->token = $token;

        return response()->json(new UserLoguedResource($dataUser), 201);
    }
    
    /**
     * Register a new user type client or therapist
     * @param \App\Http\Requests\Users\RegisterUserTherapistRequest $request
     * @return JsonResponse|mixed
     */
    public function registerTherapist(RegisterUserTherapistRequest $request): JsonResponse
    {
        $token = $this->service->registerUser($request->validated(), Role::MASSAGE_THERAPIST);
        $dataUser = clone Auth::user();
        $dataUser->token = $token;
        
        return response()->json(new UserLoguedResource($dataUser), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->service->login($request->validated());
            $dataUser = clone Auth::user();
            $dataUser->token = $token;
            return response()->json(new UserLoguedResource($dataUser));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        // ignorar error si aparece es el editor
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logout ok'], 200);
    }
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendResetPasswordLink($request->validated());
        return response()->json(['message' => 'Password reset link sent']);
    }
}
