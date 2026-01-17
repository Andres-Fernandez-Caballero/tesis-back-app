<?php

namespace App\Listeners;

use App\Events\RegisterTherapistProcessed;
use App\Mail\WelcomeTherapistMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationCreateTherapist
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
    public function handle(RegisterTherapistProcessed $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeTherapistMail($event->user));
    }
}
