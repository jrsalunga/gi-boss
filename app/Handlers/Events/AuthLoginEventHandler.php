<?php

namespace App\Handlers\Events;

use App\Events\UserLoggedIn;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class AuthLoginEventHandler
{
    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Events  $event
     * @return void
     */
    public function handle(UserLoggedIn $event)
    {
        //dd($event->request->user()->id);
        $data = [
            'ip' => clientIP(),
            'user' => $event->request->user()->name
        ];

        \Mail::mail('emails.loggedin', $data, function ($message) {
            $message->subject('User Logged In');
            $message->from('no-reply@giligansrestaurant.com', 'GI App - Boss');
            $message->to('giligans.app@gmail.com');
            $message->to('freakyash_02@yahoo.com');
        });
    }
}
