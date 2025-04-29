<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\TherapistController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function() {

    Route::get('password/test', function(Request $request){
        $email = $request->query('email');
        $password = $request->query('password');
        return response()->json([
            'message' => 'Password reset link sent to your email address.',
            'status' => Auth::attempt([
                'email' => $email,
                'password' => $password,
            ])
        ]);
    });

    Route::get('users', [UserManagementController::class, 'getAllUsers'])->name('users.all');
    Route::get('users/therapists', [UserManagementController::class, 'getAllTherapists'])->name('users.therapists');
    Route::get('users/clients', [UserManagementController::class, 'getAllClients'])->name('users.clients');

    Route::prefix('users')->middleware('auth:sanctum')->group(function () {
        Route::get('profile', [UserManagementController::class, 'viewProfile'])->name('user.profile');
        Route::put('profile', [UserManagementController::class, 'updateProfile'])->name('user.edit-profile');
    });
    
    Route::prefix('auth')
        //->middleware('')
        ->group(function () {
    Route::post('register', [AuthenticationController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('auth.login');
    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('auth.forgot-password');
    });
    
    Route::prefix('therapists')
    ->group(function(){
        Route::get('/', [TherapistController::class, 'all'])->name('therapists.all');
        Route::get('/type/{type}', [TherapistController::class, 'getAllTherapistsByType'])->name('therapists.type');
        Route::post('/', [TherapistController::class, 'store'])->name('therapists.store');
        Route::get('/{id}', [TherapistController::class, 'details'])->name('therapists.details');
    });
}); 
