<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UserAdded extends Notification
{
    use Queueable;
    protected $user;
    protected $userData;

    /**
     * Create a new notification instance.
     *
     * @param array $userData The data to be stored for the user addition notification
     */
    public function __construct(object $user)
    {
        $this->user = $user;
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->first_name,
            'user_email' => $this->user->email,
            'message' => 'A new user with ID ' . $this->user->id. ' has been added at ' . now(),
        ];
    }
}
