<?php

namespace App\Notifications;

use App\Models\FacilityReservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityReservationCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;
    protected $requester;

    public function __construct(FacilityReservation $reservation, User $requester)
    {
        $this->reservation = $reservation;
        $this->requester = $requester;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Facility Reservation')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new facility reservation has been submitted by ' . $this->requester->name . '.')
            ->line('Facility: ' . $this->reservation->facility->name)
            ->line('Reservation Title: ' . $this->reservation->title)
            ->line('Date: ' . $this->reservation->start_date->format('F d, Y') . ' to ' . $this->reservation->end_date->format('F d, Y'))
            ->line('Time: ' . $this->reservation->start_time . ' to ' . $this->reservation->end_time)
            ->action('View Reservation', route('facilities.reservations.show', $this->reservation))
            ->line('Please review and approve or reject this reservation.')
            ->line('Thank you for using CRRMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Facility Reservation',
            'message' => 'A new facility reservation has been submitted by ' . $this->requester->name . '.',
            'reservation_id' => $this->reservation->id,
            'facility_name' => $this->reservation->facility->name,
            'title' => $this->reservation->title,
            'requester_name' => $this->requester->name,
            'start_date' => $this->reservation->start_date->format('Y-m-d'),
            'end_date' => $this->reservation->end_date->format('Y-m-d'),
            'start_time' => $this->reservation->start_time,
            'end_time' => $this->reservation->end_time,
            'type' => 'facility_reservation_created',
        ];
    }
}
