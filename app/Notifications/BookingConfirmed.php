<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
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
            ->subject('Booking Confirmed: ' . $trek->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking for ' . $trek->title . ' has been successfully received.')
            ->line('Details:')
            ->line('Date: ' . $this->booking->booking_date)
            ->line('People: ' . $this->booking->number_of_people)
            ->line('Total Price: $' . $this->booking->total_price)
            ->action('View My Bookings', url('/dashboard'))
            ->line('Thank you for choosing Treak!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'trek_title' => $this->booking->trek->title,
            'message' => 'Your booking for ' . $this->booking->trek->title . ' is confirmed.',
        ];
    }
}
