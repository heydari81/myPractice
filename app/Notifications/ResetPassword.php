<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/api/password/reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your Product Management Password')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('We received a request to reset your password.')
            ->action('Reset Password Now', $url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you didn’t request this, please ignore this email.')
            ->salutation('Best regards, Product Management Team');
    }
}
?>
<?php
