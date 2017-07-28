<?php namespace App\Events\Employee;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;
use App\Models\Employee;

class ChangeStatus extends Event
{
    use SerializesModels;
    public $employee;
    public $user;
    public $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, $status=null)
    {
        $this->employee = $employee;
        $this->user = request()->user();
        $this->status = $status;
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
