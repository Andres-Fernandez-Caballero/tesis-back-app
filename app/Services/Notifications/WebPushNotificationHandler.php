<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;
use Illuminate\Support\Facades\Log;

class WebPushNotificationHandler extends NotificationHandler
{
    public function __construct(protected WebPushNotification $webPushNotification) {}

    public function sendNotification(NotificationToken $notificationToken, string $title, string $body, ?string $url = null): void
    {
        if ($notificationToken->platform === 'Web') {
            try {
                $this->webPushNotification->sendNotification($notificationToken, $title, $body);
            } catch (\Exception $e) {
                // Log el error y pasar al siguiente handler
                Log::error('WebPush notification failed: ' . $e->getMessage());
                $this->handleNext($notificationToken, $title, $body);
            }
        }

        // Pasar al siguiente handler en la cadena si existe
        $this->handleNext($notificationToken, $title, $body);
    }
}
