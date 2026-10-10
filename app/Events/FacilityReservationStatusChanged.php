<?php

namespace App\Events;

use App\Models\FacilityReservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FacilityReservationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;

    public function __construct(FacilityReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->reservation->user_id);
    }

    public function broadcastWith()
    {
        return [
            'reservation_id' => $this->reservation->id,
            'status' => $this->reservation->status,
            'facility_id' => $this->reservation->facility_id,
            'title' => $this->reservation->title,
            'start_date' => $this->reservation->start_date,
            'end_date' => $this->reservation->end_date,
        ];
    }
}
