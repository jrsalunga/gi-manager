<?php

namespace App\Events;

use App\Models\Manskedhdr as Mansked;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ManskedhdrUpdated extends Event
{
    use SerializesModels;

    public $id;
    public $isCompleted;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Mansked $mansked)
    {
        $this->id = $mansked->id;
        $this->isCompleted = (bool) $item->isCompleted;
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
