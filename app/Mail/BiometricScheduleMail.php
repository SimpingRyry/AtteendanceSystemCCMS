<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BiometricScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $date;
    public $orgName;

    public function __construct($student, $date, $orgName)
    {
        $this->student = $student;
        $this->date = $date;
        $this->orgName = $orgName;
    }

    public function build()
    {
        return $this->subject('Biometric Registration Schedule Notification')
                    ->view('emails.biometric_schedule');
    }
}
