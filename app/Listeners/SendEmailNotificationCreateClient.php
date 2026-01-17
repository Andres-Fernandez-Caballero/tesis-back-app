<?php

namespace App\Listeners;

use App\Events\RegisterClientProcessed;
use App\Mail\WelcomeClientMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationCreateClient
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterClientProcessed $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeClientMail($event->user));
    }
}
