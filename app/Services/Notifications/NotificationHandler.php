<?php

namespace App\Services\Notifications;

use App\Models\NotificationToken;

abstract class NotificationHandler implements Notificable
{
    protected ?NotificationHandler $next = null;

    public function setNext(NotificationHandler $handler): NotificationHandler
    {
        $this->next = $handler;
        return $handler;
    }

    /**
     * Pasa la responsabilidad al siguiente handler en la cadena
     */
    protected function handleNext(NotificationToken $token, string $title, string $body): void
    {
        if (isset($this->next)) $this->next->sendNotification($token, $title, $body);
    }

    /**
     * Envía la notificación
     * Cada implementación debe manejar su lógica y opcionalmente pasar al siguiente handler
     * 
     * @param NotificationToken $token Token del dispositivo
     * @param string $title Título de la notificación
     * @param string $body Cuerpo de la notificación
     * @param string|null $url URL opcional para la notificación
     */
    abstract public function sendNotification(NotificationToken $token, string $title, string $body, ?string $url = null): void;
}
