<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmailNotification extends VerifyEmail
{
    public function build($notifiable)
    {
        return (new MailMessage)
            ->from('concertnation@mail.com', 'CONCERTNATION')
            ->subject('Verify Email Address')
            ->markdown('emails.verify-email', [
                'url' => $this->verificationUrl($notifiable),
            ]);
    }
}
