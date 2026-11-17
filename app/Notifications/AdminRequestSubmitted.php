<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AdminRequestSubmitted extends Notification
{
    public const RESOURCE_PEMINJAMAN = 'peminjaman';

    public const RESOURCE_LAPORAN = 'laporan';

    public function __construct(
        public readonly string $resource,
        public readonly int $recordId,
        public readonly string $title,
        public readonly string $message,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, int|string>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'resource' => $this->resource,
            'record_id' => $this->recordId,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
