<?php namespace App\Listeners;

use Illuminate\Contracts\Mail\Mailer;

class EmployeeEventListener
{

  private $mailer;

  public function __construct(Mailer $mailer) {
    $this->mailer = $mailer;
  }

  /**
   * Handle Employee Resigned events.
   */
  public function onResigned($event) {

    $data = [
      'code'      => $event->employee->code,
      'fullname'  => $event->employee->lastname.', '. $event->employee->firstname,
      'brcode'    => request()->user()->branch->code,
    ];

    $this->mailer->queue('emails.employee.resigned', $data, function ($message) use ($event, $data){
      $message->subject('Resigned Employee: '. $data['code'] . ' [resign]');
      $message->from($event->user->email, $event->user->name.' ('.$event->user->email.')');
      $message->to('giligans.app@gmail.com');
    });
  }

  public function subscribe($events) {
    $events->listen(
      'App\Events\Employee\Resigned',
      'App\Listeners\EmployeeEventListener@onResigned'
    );
  }
}


