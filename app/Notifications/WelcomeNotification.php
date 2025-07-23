<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('Welcome to ' . config('app.name', 'OnlyFans Marketplace'))
            ->greeting('Welcome to the OnlyFans Professional Marketplace, ' . $notifiable->name . '!')
            ->line('Thank you for joining our professional marketplace where OnlyFans creators, agencies, and service providers connect.')
            ->line('Here\'s what you can do on our platform:')
            ->line('• Browse thousands of professional profiles')
            ->line('• Connect with talented creators and agencies')
            ->line('• Post or apply to job opportunities')
            ->line('• Build your professional network')
            ->line('• Showcase your skills and expertise')
            ->action('Explore the Marketplace', route('marketplace.index'))
            ->line('We\'re excited to have you as part of our growing community!')
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, The ' . config('app.name', 'Marketplace') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Welcome to the platform!',
            'user_name' => $notifiable->name,
        ];
    }
}
