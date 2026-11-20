<?php

namespace App\Notifications;

use App\Models\FacilityReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityReservationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;
    protected $reason;

    public function __construct(FacilityReservation $reservation, $reason = null)
    {
        $this->reservation = $reservation;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Facility Reservation Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your facility reservation for ' . $this->reservation->facility->name . ' has been rejected.')
            ->line('Reservation Title: ' . $this->reservation->title)
            ->line('Requested Dates: ' . $this->reservation->start_date->format('F d, Y') . ' to ' . $this->reservation->end_date->format('F d, Y'))
            ->line('Time: ' . $this->reservation->start_time . ' to ' . $this->reservation->end_time);

        if ($this->reason) {
            $mailMessage->line('Reason: ' . $this->reason);
        }

        $mailMessage->action('View Reservation', route('facilities.reservations.show', $this->reservation))
            ->line('You may submit a new reservation with different dates or details.')
            ->line('Thank you for using CRRMS!');

        return $mailMessage;
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Facility Reservation Rejected',
            'message' => 'Your facility reservation for ' . $this->reservation->facility->name . ' has been rejected.',
            'reservation_id' => $this->reservation->id,
            'facility_name' => $this->reservation->facility->name,
            'title' => $this->reservation->title,
            'rejection_reason' => $this->reason,
            'type' => 'facility_reservation_rejected',
        ];
    }
}
