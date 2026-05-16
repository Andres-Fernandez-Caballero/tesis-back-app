<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;

interface Notificable
{
    public function sendNotification(NotificationToken $notificationToken, string $title, string $body, ?string $url = null): void;
}