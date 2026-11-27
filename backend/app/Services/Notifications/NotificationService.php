<?php

namespace App\Services\Notifications;

use App\Events\Notifications\NotificationCreated;
use App\Http\Resources\Api\Notifications\NotificationResource;
use App\Models\User;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    public function send(?User $recipient, LaravelNotification $notification): void
    {
        if (! $recipient) {
            return;
        }

        // 1. Persist the in-app notification synchronously so the bell updates immediately.
        $recipient->notifyNow($notification, ['database']);

        // 2. Fetch the persisted DatabaseNotification model for its exact UUID and payload.
        $persisted = $recipient->notifications()
            ->whereKey($notification->id)
            ->first();

        // 3. Broadcast to Reverb safely without failing the HTTP request if WebSockets are down.
        if ($persisted) {
            try {
                event(new NotificationCreated(
                    userId: (int) $recipient->getKey(),
                    notification: (new NotificationResource($persisted))->resolve(),
                ));
            } catch (Throwable $exception) {
                Log::warning('Unable to broadcast a user notification.', [
                    'user_id' => $recipient->getKey(),
                    'notification_id' => $persisted->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        // 4. Queue email delivery separately (relies on via() returning ['mail']).
        if (filled($recipient->email)) {
            $recipient->notify($notification);
        }
    }
}
