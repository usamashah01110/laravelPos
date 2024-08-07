<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SentVerifyEmail extends Notification
{
    use Queueable;

    protected $verificationData;

    /**
     * Create a new notification instance.
     *
     * @param array $verificationData The data to be stored for email verification
     */
    public function __construct(array $verificationData)
    {
        $this->verificationData = $verificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id' => $this->verificationData['user_id'],
            'email' => $this->verificationData['email'],
            'code' => $this->verificationData['code'],
            'codeExpiry' => $this->verificationData['codeExpiry'],
            'message' => 'Email verification code has been sent to ' . $this->verificationData['email'] . ' at ' . now(),
        ];
    }
}
