<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SubscriptionSuccess extends Notification
{
    use Queueable;

    protected $subscriptionData;

    /**
     * Create a new notification instance.
     *
     * @param array $subscriptionData The subscription data to be included in the notification
     */
    public function __construct(array $subscriptionData)
    {
        $this->subscriptionData = $subscriptionData;
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
     * Get the array representation of the notification to be stored in the database.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id' => $notifiable->id,
            'plan' => $this->subscriptionData['plan'],
            'amount' => $this->subscriptionData['amount'],
            'message' => 'Subscription Successful!',
        ];
    }
}
