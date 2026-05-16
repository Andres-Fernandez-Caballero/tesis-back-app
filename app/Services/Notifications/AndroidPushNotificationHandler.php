<?php 

namespace App\Services\Notifications;

use App\Models\NotificationToken;

class AndroidPushNotificationHandler extends NotificationHandler
{
    public function __construct(protected AndroidPushNotification $androidPushNotification)
    {
    }

    public function sendNotification(NotificationToken $notificationToken, string $title, string $body, ?string $url = null): void
    {
        // Lógica para enviar notificaciones push a dispositivos Android
        if ($notificationToken->platform === 'Android') {
            $this->androidPushNotification->sendNotification($notificationToken, $title, $body);
        }

        // Pasar al siguiente handler en la cadena si existe
        $this->handleNext($notificationToken, $title, $body);        
    }
}