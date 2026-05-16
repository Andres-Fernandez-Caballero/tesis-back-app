<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;

class IosPushNotificationHandler extends NotificationHandler
{
    public function __construct(protected IosPushNotification $iosPushNotification)
    {
    }
    public function sendNotification(NotificationToken $token, string $title, string $body, ?string $url = null): void
    {
        // Lógica para enviar notificaciones push a dispositivos iOS
        if ($token->platform === 'iOS') {
            $this->iosPushNotification->sendNotification($token, $title, $body);
        }
        // Pasar al siguiente handler en la cadena si existe
        $this->handleNext($token, $title, $body);
    }
}