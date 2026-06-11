<?php

use App\Http\Controllers\LocalRegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/terminos-y-condiciones', function () {
    return view('terms');
})->name('terms');

Route::get('/politica-de-privacidad', function () {
    return view('privacy');
})->name('privacy');

Route::post('/registrar-local', [LocalRegistrationController::class, 'store'])->name('registrar-local.store');

// TODO: eliminar rutas en un futuro
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

//     Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
//     Volt::route('settings/password', 'settings.password')->name('settings.password');
//     Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
// });

require __DIR__ . '/auth.php';
