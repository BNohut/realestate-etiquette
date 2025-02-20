<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Models\UserVerify;
use App\Notifications\VerifyNotification;
use Illuminate\Support\Str;

class SendVerifyEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredEvent $event): void
    {

        $token = Str::random(60);

        UserVerify::create([
            'user_id' => $event->user->id,
            'token' => $token
        ]);
        $event->user->notify(new VerifyNotification($token));
//        Mail::to($event->user->email)->send(new  verifyMail($event->user, $token));
    }
}
