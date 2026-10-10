<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $serviceRequest;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->serviceRequest->requested_by);
    }

    public function broadcastWith()
    {
        return [
            'request_id' => $this->serviceRequest->id,
            'status' => $this->serviceRequest->status,
            'type' => $this->serviceRequest->type,
            'title' => $this->serviceRequest->title,
            'priority' => $this->serviceRequest->priority,
        ];
    }
}
