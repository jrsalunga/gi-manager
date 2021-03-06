<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;

class UserLoggedIn extends Event
{
    use SerializesModels;
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //$this->request = $request;
        $browser = getBrowserInfo();
        $this->request = $request->all();
        array_set($this->request, 'name', $request->user()->name);
        array_set($this->request, 'ip', clientIP());
        array_set($this->request, 'browser', $browser['browser']);
        array_set($this->request, 'platform', $browser['platform']);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
