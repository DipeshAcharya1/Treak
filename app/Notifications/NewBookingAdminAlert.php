<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingAdminAlert extends Notification
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
        $user = $this->booking->user;
        return (new MailMessage)
            ->subject('New Booking Alert: ' . $trek->title)
            ->line('A new booking has been placed on the Treak platform.')
            ->line('Customer: ' . $user->name . ' (' . $user->email . ')')
            ->line('Trek: ' . $trek->title)
            ->line('Date: ' . $this->booking->booking_date)
            ->line('Amount: $' . $this->booking->total_price)
            ->action('Manage Bookings', url('/admin'))
            ->line('Please review the booking in the admin panel.');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'user_name' => $this->booking->user->name,
            'trek_title' => $this->booking->trek->title,
            'message' => 'New booking from ' . $this->booking->user->name . ' for ' . $this->booking->trek->title,
        ];
    }
}
