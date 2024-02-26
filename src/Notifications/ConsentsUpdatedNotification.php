<?php

namespace Visualbuilder\FilamentUserConsent\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Visualbuilder\FilamentUserConsent\Mail\ConsentsUpdatedMail;

class ConsentsUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('filament-user-consent.notify');
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        // Retrieve the class name from the package configuration
        $mailableClass = config('filament-user-consent.mailable');

        // Dynamically instantiate the Mailable class with the $notifiable data
        $mailable = new $mailableClass($notifiable);

        return $mailable;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
