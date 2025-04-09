<?php

use App\Http\Controllers\Api\TherapistController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('profile', [UserManagementController::class, 'viewProfile'])->name('user.profile');
    Route::put('profile', [UserManagementController::class, 'updateProfile'])->name('user.edit-profile');
});

Route::prefix('auth')
    //->middleware('')
    ->group(function () {
Route::post('register', [UserManagementController::class, 'register'])->name('auth.register');
Route::post('login', [UserManagementController::class, 'login'])->name('auth.login');
Route::post('forgot-password', [UserManagementController::class, 'forgotPassword'])->name('auth.forgot-password');
});

Route::prefix('therapists')
->group(function(){
    Route::get('/', [TherapistController::class, 'all'])->name('therapists.all');
    Route::post('/', [TherapistController::class, 'store'])->name('therapists.store');
});