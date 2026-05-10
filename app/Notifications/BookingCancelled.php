<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $trek = $this->booking->trek;
        return (new MailMessage)
            ->subject('Booking Cancelled: ' . $trek->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking for ' . $trek->title . ' on ' . $this->booking->booking_date . ' has been cancelled.')
            ->line('If you did not request this, please contact our support.')
            ->action('Explore More Treks', url('/treks'))
            ->line('We hope to see you on another adventure soon!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'trek_title' => $this->booking->trek->title,
            'message' => 'Your booking for ' . $this->booking->trek->title . ' has been cancelled.',
        ];
    }
}
