<?php

namespace App\Channels;

use GuzzleHttp\Client;

class BrevoChannel
{
    public function send($notifiable, $notification)
    {
        $message = $notification->toBrevo($notifiable);

        $client = new Client();
        $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => config('services.brevo.key'),
                'Content-Type' => 'application/json',
            ],
            'json' => $message,
        ]);
    }
}