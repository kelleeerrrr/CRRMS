<?php

namespace App\Events;

use App\Models\MaintenanceLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceRequestStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $maintenanceLog;

    public function __construct(MaintenanceLog $maintenanceLog)
    {
        $this->maintenanceLog = $maintenanceLog;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->maintenanceLog->created_by);
    }

    public function broadcastWith()
    {
        return [
            'maintenance_id' => $this->maintenanceLog->id,
            'status' => $this->maintenanceLog->status,
            'type' => $this->maintenanceLog->type,
            'title' => $this->maintenanceLog->title,
            'priority' => $this->maintenanceLog->priority,
        ];
    }
}
