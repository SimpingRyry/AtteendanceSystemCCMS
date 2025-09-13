<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class CustomVerifyEmail extends VerifyEmailNotification
{
    protected $isOfficer;
    protected $studentId;

    public function __construct($isOfficer, $studentId)
    {
        $this->isOfficer = $isOfficer;
        $this->studentId = $studentId;
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }

    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        $mail = (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Please click the button below to verify your email address.');

        // Add account details
        $mail->line('**Your login details:**')
             ->line('Email: ' . $notifiable->email);

        if ($this->isOfficer) {
            $mail->line('Officer Account Password: Your email address')
                 ->line('Member Account Password: Your student ID (' . $this->studentId . ')')
                 ->line('*Note: Your officer account will expire once your service year ends.*');
        } else {
            $mail->line('Password: Your student ID (' . $this->studentId . ')');
        }

        $mail->action('Verify Email Address', $url)
             ->line('If you did not create an account, no further action is required.')
             ->salutation('Regards, Laravel');

        return $mail;
    }
}
