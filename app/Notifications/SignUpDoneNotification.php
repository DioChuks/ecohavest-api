<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignUpDoneNotification extends Notification
{
    use Queueable;

    private $checkUserOtp;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $checkUserOtp)
    {
        $this->checkUserOtp = $checkUserOtp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Ecohavest!')
                    ->greeting('Hey ' . $notifiable->first_name)
                    ->line('We are glad to have you here on our platform!')
                    ->action('Here is our Official Website!', url('https://ecohavest.org'))
                    ->line('This is a no reply message')
                    ->line('If you did not create an account, Contact us at support@ecohavest.org');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
