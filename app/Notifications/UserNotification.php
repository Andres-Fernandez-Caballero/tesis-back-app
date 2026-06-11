<?php

namespace App\Notifications;

use App\Broadcasting\PushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public ?string $view = null,
        public array $viewData = [],
        public array $data = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', PushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->view) {
            return (new MailMessage)
                ->subject($this->title)
                ->view($this->view, $this->viewData);
        }

        return (new MailMessage)
            ->subject($this->title)
            ->line($this->body)
            ->action('Ver detalles', $this->url ?? url('/'));
    }

    public function toPush(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => $this->url,
            'data'  => $this->data,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => $this->url,
            'data'  => $this->data,
        ];
    }
}
