<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected NotificationHandler $handler;

    public function __construct(
        protected WebPushNotificationHandler $webPushNotificationHandler,
        protected AndroidPushNotificationHandler $androidNotificationHandler,
        protected IosPushNotificationHandler $iosNotificationHandler,
        protected WebPushNotification $webPush
    ) {
        // Configurar la cadena de responsabilidad
        $this->webPushNotificationHandler
            ->setNext($this->androidNotificationHandler)
            ->setNext($this->iosNotificationHandler);

        $this->handler = $this->webPushNotificationHandler;
    }

    public function registerToken(User $user, array $data)
    {
        NotificationToken::updateOrCreate([
            'user_id' => $user->id,
            'platform' => $data['platform'],
            'endpoint' => $data['endpoint'] ?? null,
            'keys' => $data['keys'] ?? null,
            'token' => $data['token'] ?? null,
        ]);
    }

    public function send(User $user, string $title, string $body, ?string $url = null)
    {

        $user->notificationTokens()->each(function (NotificationToken $notificationToken) use ($title, $body, $url) {
            Log::info('token notificado', [
                'token' => $notificationToken,
                'title' => $title,
            ]);

            $this->webPush->sendNotification($notificationToken, $title, $body, $url);
        });
    }
}
