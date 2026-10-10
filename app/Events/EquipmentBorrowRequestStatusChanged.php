<?php

namespace App\Events;

use App\Models\EquipmentBorrowRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EquipmentBorrowRequestStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $borrowRequest;

    public function __construct(EquipmentBorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->borrowRequest->user_id);
    }

    public function broadcastWith()
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'status' => $this->borrowRequest->status,
            'purpose' => $this->borrowRequest->purpose,
            'start_date' => $this->borrowRequest->start_date,
            'end_date' => $this->borrowRequest->end_date,
        ];
    }
}
