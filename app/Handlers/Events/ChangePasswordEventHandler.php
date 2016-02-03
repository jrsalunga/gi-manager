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
            'ip' => 'change password',
            'user' => $event->request->user()->name
        ];

        \Mail::send('emails.loggedin', $data, function ($message) {
            $message->subject('User Change Password');
            $message->from('no-reply@giligansrestaurant.com', 'Giligan\'s Web App');
            $message->to('giligans.app@gmail.com');
            $message->to('freakyash_02@yahoo.com');
        });
    }
}
