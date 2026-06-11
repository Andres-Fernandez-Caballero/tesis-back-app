<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
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
        $platform = $data['platform'];

        if ($platform === 'web') {
            // Web push: el endpoint es el identificador único de la suscripción
            NotificationToken::updateOrCreate(
                ['endpoint' => $data['endpoint']],
                [
                    'user_id'  => $user->id,
                    'platform' => $platform,
                    'keys'     => $data['keys'] ?? null,
                    'token'    => null,
                ]
            );
        } else {
            // iOS / Android: un token por usuario+plataforma
            NotificationToken::updateOrCreate(
                ['user_id' => $user->id, 'platform' => $platform],
                [
                    'token'    => $data['token'] ?? null,
                    'endpoint' => null,
                    'keys'     => null,
                ]
            );
        }
    }

    public function send(User $user, string $title, string $body, ?string $url = null, array $data = []): void
    {
        $user->notificationTokens()->each(function (NotificationToken $token) use ($title, $body, $url, $data) {
            try {
                Log::info('sending notification', ['platform' => $token->platform, 'title' => $title]);

                if ($token->platform === 'web' && $token->endpoint) {
                    $this->webPush->sendNotification($token, $title, $body, $url);
                } elseif (in_array($token->platform, ['ios', 'android']) && $token->token) {
                    Http::post('https://exp.host/api/v2/push/send', [
                        'to'    => $token->token,
                        'title' => $title,
                        'body'  => $body,
                        'sound' => 'default',
                        'data'  => array_merge($data, $url ? ['url' => $url] : []),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending push notification', [
                    'token_id' => $token->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        });
    }
}
