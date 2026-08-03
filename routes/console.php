<?php

use App\Console\Commands\CancelUnpaidBookings;
use App\Console\Commands\ExpireStaleBookings;
use App\Console\Commands\ResetMonthlySubscriptions;
use App\Repositories\UserRepository;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Expira turnos pending/confirmed cuya fecha ya pasó — corre cada día a las 00:05
Schedule::command(ExpireStaleBookings::class)
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expire-bookings.log'));

// Cancela reservas en pending_payment sin pagar en 8 minutos — corre cada 2 minutos
Schedule::command(CancelUnpaidBookings::class)
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/cancel-unpaid-bookings.log'));

// Reinicia a pago pendiente las suscripciones activas del período vencido — corre el 1 de cada mes
Schedule::command(ResetMonthlySubscriptions::class)
    ->monthlyOn(1, '00:10')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reset-subscriptions.log'));

// Desbaneos de usuarios
Schedule::call(function () {
    $userManagementService = new UserManagementService(new UserRepository());
    $userManagementService->unBanUsers();
})->daily();

// Heartbeat: confirma en Slack que el scheduler sigue corriendo — cada 30 minutos
Schedule::call(function () {
    Log::channel('slack')->info('cron funcionando');
})->everyThirtyMinutes();