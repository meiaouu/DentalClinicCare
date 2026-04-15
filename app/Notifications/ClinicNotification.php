<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ClinicNotification extends Notification
{
    public function __construct(protected array $data)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Notification',
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? '#',
            'type' => $this->data['type'] ?? 'info',
        ];
    }
}
