<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct()
    {
        $this->onQueue('mail');
    }

    /**
     * Exponential backoff in seconds before retrying failed email deliveries.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    protected function frontendUrl(string $path = ''): string
    {
        $base = rtrim((string) config('app.frontend_url', config('app.url')), '/');
        $cleanPath = '/' . ltrim($path, '/');

        return $path === '' ? $base : $base . $cleanPath;
    }
}
