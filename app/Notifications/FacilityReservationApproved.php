<?php

namespace App\Notifications;

use App\Models\FacilityReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityReservationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;

    public function __construct(FacilityReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Facility Reservation Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your facility reservation for ' . $this->reservation->facility->name . ' has been approved.')
            ->line('Reservation Title: ' . $this->reservation->title)
            ->line('Date: ' . $this->reservation->start_date->format('F d, Y') . ' to ' . $this->reservation->end_date->format('F d, Y'))
            ->line('Time: ' . $this->reservation->start_time . ' to ' . $this->reservation->end_time)
            ->action('View Reservation', route('facilities.reservations.show', $this->reservation))
            ->line('Please arrive on time for your reservation.')
            ->line('Thank you for using CRRMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Facility Reservation Approved',
            'message' => 'Your facility reservation for ' . $this->reservation->facility->name . ' has been approved.',
            'reservation_id' => $this->reservation->id,
            'facility_name' => $this->reservation->facility->name,
            'title' => $this->reservation->title,
            'start_date' => $this->reservation->start_date->format('Y-m-d'),
            'end_date' => $this->reservation->end_date->format('Y-m-d'),
            'type' => 'facility_reservation_approved',
        ];
    }
}
