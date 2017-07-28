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
      'subject'  => 'Employee Resigned: '.$event->employee->code,
      'body'  => $event->employee->lastname.', '. $event->employee->firstname .' resigned at '.request()->user()->branch->code
    ];

    $this->generalMailer($event, $data, 'resign');
  }

  public function subscribe($events) {
    $events->listen(
      'App\Events\Employee\Resigned',
      'App\Listeners\EmployeeEventListener@onResigned'
    );
  }


  private function generalMailer($event, $data, $tag=NULL) {

    $this->mailer->queue('emails.general', $data, function ($message) use ($event, $data, $tag) {

      $subject = $data['subject'];

      if (!is_null($tag))
        $subject .= ' ['.$tag.']';

      $message->subject($subject);
      $message->from($event->user->email, $event->user->name.' ('.$event->user->email.')');
      $message->to('giligans.app@gmail.com');
    
    });
  
  }
}


