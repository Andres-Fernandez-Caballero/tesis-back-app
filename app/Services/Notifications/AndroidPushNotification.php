<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;
use Illuminate\Support\Facades\Log;

class AndroidPushNotification implements Notificable
{
    public function sendNotification(NotificationToken $notificationToken, string $title, string $body, ?string $url = null): void
    {
        Log::info("Sending Android push notification to token: {$notificationToken->token} with title: {$title} and body: {$body}");
    }
}