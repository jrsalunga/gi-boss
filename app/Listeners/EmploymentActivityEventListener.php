<?php namespace App\Listeners;

use Illuminate\Contracts\Mail\Mailer;
use App\Helpers\BossBranch;

class EmploymentActivityEventListener
{

  private $mailer;
  private $bossBranch;
  private $fileStorage;

  public function __construct(Mailer $mailer, BossBranch $bossBranch) {
    $this->mailer = $mailer;
    $this->bossBranch = $bossBranch;
    $this->fileStorage = app()->fileStorage;
  }

  
  public function handleExportRequest($event) {
    //test_log(json_encode($event));
    
    $e = [];
    $am_email      = app()->environment('production') ? $this->bossBranch->getFirstUser($event->empActivity->branch_id) : env('DEV_AM_MAIL');
    $csh_email     = app()->environment('production') ? $event->empActivity->branch->email : env('DEV_CSH_MAIL');
    $to_csh_email  = app()->environment('production') ? $event->empActivity->branchto->email : env('DEV_TO_CSH_MAIL');
    $hr_email      = app()->environment('production') ? env('HR_MAIL') : env('DEV_HR_MAIL');

    // make uniform subject
    $subject = $event->data['type'].' '.$event->data['trail'].' '.$event->data['manno'].' '.$event->data['fullname'];
    

    // 1. notify the receiveing cashier
    // if ($event->empActivity->status==1) {
    //   $e['subject'] = 'IMREQ '.$subject;
    //   $e['to'] = $to_csh_email;
    //   $e['body'] = 'This is to notify that export request for transfer of '. $event->data['manno'].' '.$event->data['fullname'].' from '..' to '..' has been approved by HR. Please prepare the';
      
    //   $this->mailer->send('emails.notifier', $e, function ($message) use ($e) {
    //     $message->subject($e['subject']);
    //     $message->from('giligans.app@gmail.com', 'Giligans HRIS');
    //     $message->to($e['to']);
    //   });
    // } 


    // 2. me (RM/AM)
    $e['subject'] = $event->data['brcode'].' '.$subject;
    $e['to'] = $am_email;
    $e['body'] = $event->empActivity->status == 3 
      ? 'This is to notify that export request has been declined by the HR.'
      : 'Your branch export request was approved by the HR. Passkey has been sent to '.$event->data['brcode'].'.';
    
    $this->mailer->send('emails.notifier', $e, function ($message) use ($e) {
      $message->subject($e['subject']);
      $message->from('giligans.app@gmail.com', 'Giligans HRIS');
      $message->to($e['to']);
    });

    
    // 3. email cashier with the passkey.
    $e['to'] = $csh_email;
    $e['replyTo'] = $hr_email;
    $e['replyToName'] = session('user.fullname');
    $e['attachment'] = NULL;
    $e['brcode'] = $event->data['brcode'];
    $e['fullname'] = $event->data['manno'].' '.$event->data['fullname'];
    $e['cashier'] = $event->data['cashier'];
    $e['notes'] = $event->data['notes'];
    $e['link'] =  'http://gi-cashier.loc/download/passkey/'.$event->empActivity->lid();
    $e['logo'] = 'http://boss.giligansrestaurant.com/images/giligans-header.png';
    $e['href'] = 'http://boss.giligansrestaurant.com/hr/masterfiles/employee/'.$event->empActivity->employee->lid();

    //$locator = new Locator('files');
    if ($this->fileStorage->exists($event->empActivity->file_path)) 
      $e['attachment'] = $this->fileStorage->realFullPath($event->empActivity->file_path);

    $this->mailer->send('emails.emp_activity.am_exreq_sent', $e, function ($message) use ($e) {
      $message->subject($e['subject']);
      $message->from('giligans.app@gmail.com', 'Giligans HRIS');
      $message->to($e['to']);
      $message->replyTo($e['replyTo'], $e['replyToName']);
      //$message->cc('giligans.hris@gmail.com');
      
      if (!is_null($e['attachment']))
        $message->attach($e['attachment']);
    });

    





  }

  public function subscribe($events) {

    $events->listen(
      'App\Events\EmploymentActivity\ExportRequest',
      'App\Listeners\EmploymentActivityEventListener@handleExportRequest'
    );
  }
}


