<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotification implements Notificable
{
    public function sendNotification(NotificationToken $notificationToken, string $title, string $body, ?string $url = null): void
    {
        
        $webPush = new WebPush([
        'VAPID' => [
        'subject' => config('webpush.admin_email'),
        'publicKey' => config('webpush.public_key'),
        'privateKey' => config('webpush.private_key'),
        ],
        ]);
        
        Log::alert('data', [$webPush]);

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
        ]);

        $subscription = Subscription::create([
            'endpoint' => $notificationToken->endpoint,
            'keys' => [
                'p256dh' => $notificationToken->keys['p256dh'],
                'auth' => $notificationToken->keys['auth'],
            ],
        ]);

        $webPush->sendOneNotification(
            subscription: $subscription,
            payload: $payload
        );


        foreach ($webPush->flush() as $report) {
            // logging opcional
            Log::info('detalle', [$report]);
        }
    }
}
