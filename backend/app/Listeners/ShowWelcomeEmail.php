<?php

namespace App\Listeners;

use App\Mail\WelcomeUserMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'mail';

    public int $tries = 3;

    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (! method_exists($user, 'getAttribute') || blank($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->send(new WelcomeUserMail($user));
        } catch (Throwable $exception) {
            Log::warning('Failed to queue or send welcome email.', [
                'user_id' => $user->getAuthIdentifier(),
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
