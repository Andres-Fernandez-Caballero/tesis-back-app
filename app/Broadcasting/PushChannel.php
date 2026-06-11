<?php

namespace App\Broadcasting;

use App\Enums\Role;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use App\Services\Notifications\WebPushNotification;

class PushChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct(protected NotificationService $pushNotificationService) {}

    public function send($notifiable, $notification)
    {
        if(!method_exists($notification, 'toPush')) {
            return;
        }

        $data = $notification->toPush($notifiable);

        $this->pushNotificationService->send(
            user: $notifiable,
            title: $data['title'],
            body:  $data['body'],
            url:   $data['url'] ?? null,
            data:  $data['data'] ?? [],
        );
    }


    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        /*
        return $user->hasRole(Role::CLIENT)
            ? ['id' => $user->id]
            : false;
        */
        return true;
    }
}
