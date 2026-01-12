<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\TherapistController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function() {

    require __DIR__ . '/api/v1/_index.php';

    // TODO: Remove this test route later
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



    
    
    
    
}); 
