<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SinglePaymentSuccess extends Notification
{
    use Queueable;

    protected $paymentData;

    /**
     * Create a new notification instance.
     *
     * @param array $paymentData The payment data to be included in the notification
     */
    public function __construct(array $paymentData)
    {
        $this->paymentData = $paymentData;
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
            'product' => $this->paymentData['product'],
            'amount' => $this->paymentData['price'],
            'message' => 'Payment Successful!',
        ];
    }
}
