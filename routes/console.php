<?php

use App\Console\Commands\SysteActions\UnbanUsersCommand;
use App\Repositories\UserRepository;
use App\Services\UserManagementService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::call(function(){
    echo "Unbanning users...";
    $userManagementService = new UserManagementService(new UserRepository());
    $userManagementService->unBanUsers();
});