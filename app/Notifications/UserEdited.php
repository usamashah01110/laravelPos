<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UserEdited extends Notification
{
    use Queueable;


    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param object $user The data to be stored for the user edited notification
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
            'message' => 'User with ID ' . $this->user->id . ' has been edited at ' . now(),
        ];
    }
}
