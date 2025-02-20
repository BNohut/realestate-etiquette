<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogInOut
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin($event)
    {
        if (request()->has('push_token')) {
            $user = User::find($event->user->id);
            $user->push_token = request()->push_token;
            $user->save();
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event)
    {
        $user = User::find($event->user->id);
        $user->push_token = null;
        $user->save();
    }

    /**
     * subscribe the event.
     *
     * @param  object  $event
     * @return void
     */
    public function subscribe($event)
    {
        $event->listen(
            Login::class,
            [LogInOut::class, 'handleUserLogin']
        );

        $event->listen(
            Logout::class,
            [LogInOut::class, 'handleUserLogout']
        );
    }
}
