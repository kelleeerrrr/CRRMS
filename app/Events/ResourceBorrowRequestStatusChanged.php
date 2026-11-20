<?php

namespace App\Events;

use App\Models\ResourceBorrowRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceBorrowRequestStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $borrowRequest;

    public function __construct(ResourceBorrowRequest $borrowRequest)
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
            'resource_name' => $this->borrowRequest->resource->name,
            'purpose' => $this->borrowRequest->purpose,
            'start_date' => $this->borrowRequest->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->borrowRequest->end_date?->format('Y-m-d H:i:s'),
            'approved_by' => $this->borrowRequest->approved_by,
            'rejection_reason' => $this->borrowRequest->rejection_reason,
        ];
    }
}
