<?php

namespace App\Notifications;

use App\Models\EquipmentBorrowRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipmentBorrowRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $requester;

    public function __construct(EquipmentBorrowRequest $request, User $requester)
    {
        $this->request = $request;
        $this->requester = $requester;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Equipment Borrow Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new equipment borrow request has been submitted by ' . $this->requester->name . '.')
            ->line('Requester: ' . $this->requester->name)
            ->line('Purpose: ' . $this->request->purpose)
            ->line('Start Date: ' . $this->request->start_date->format('F d, Y H:i'))
            ->line('End Date: ' . $this->request->end_date->format('F d, Y H:i'))
            ->action('View Request', route('equipment-borrow-requests.show', $this->request))
            ->line('Please review and approve or reject this request.')
            ->line('Thank you for using CRRMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Equipment Borrow Request',
            'message' => 'A new equipment borrow request has been submitted by ' . $this->requester->name . '.',
            'request_id' => $this->request->id,
            'requester_name' => $this->requester->name,
            'purpose' => $this->request->purpose,
            'start_date' => $this->request->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->request->end_date->format('Y-m-d H:i:s'),
            'type' => 'equipment_borrow_request_created',
        ];
    }
}
