<?php

namespace App\Notifications;

use App\Models\ResourceBorrowRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceBorrowRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $requester;

    public function __construct(ResourceBorrowRequest $request, User $requester)
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
            ->subject('New Resource Borrow Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new resource borrow request has been submitted by ' . $this->requester->name . '.')
            ->line('Resource: ' . $this->request->resource->name)
            ->line('Quantity: ' . $this->request->quantity)
            ->line('Purpose: ' . $this->request->purpose)
            ->line('Start Date: ' . $this->request->start_date->format('F d, Y H:i'))
            ->line('End Date: ' . $this->request->end_date->format('F d, Y H:i'))
            ->action('View Request', route('resource-borrow-requests.show', $this->request))
            ->line('Please review and approve or reject this request.')
            ->line('Thank you for using CRRMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Resource Borrow Request',
            'message' => 'A new resource borrow request has been submitted by ' . $this->requester->name . '.',
            'request_id' => $this->request->id,
            'resource_name' => $this->request->resource->name,
            'quantity' => $this->request->quantity,
            'requester_name' => $this->requester->name,
            'purpose' => $this->request->purpose,
            'start_date' => $this->request->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->request->end_date->format('Y-m-d H:i:s'),
            'type' => 'resource_borrow_request_created',
        ];
    }
}
