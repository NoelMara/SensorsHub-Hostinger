<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\BrevoChannel;

class VerifyEmailWithCode extends Notification
{
    use Queueable;

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via(object $notifiable): array
    {
        return [BrevoChannel::class];
    }

    public function toBrevo(object $notifiable): array
    {
        return [
            'sender' => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to' => [
                ['email' => $notifiable->email]
            ],
            'subject' => 'Email Verification Code',
            'htmlContent' => '
                <p>Hello ' . $notifiable->name . '!</p>
                <p>Thank you for registering with SensorsHub. Use the verification code below:</p>
                <h2 style="letter-spacing:4px;">' . $this->code . '</h2>
                <p>This code will expire in 10 minutes.</p>
                <p>If you did not create an account, no further action is required.</p>
                <p>Best regards, SensorsHub Team</p>
            ',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}