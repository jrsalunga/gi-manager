<?php

namespace App\Events;

use App\Models\Manskedhdr as Mansked;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ManskedhdrCreated extends Event
{
    use SerializesModels;

    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Mansked $mansked)
    {
        $this->id = $mansked->id;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['manskedAction'];
    }
}
