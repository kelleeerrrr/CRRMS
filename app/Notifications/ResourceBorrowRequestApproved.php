<?php

namespace App\Notifications;

use App\Models\ResourceBorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceBorrowRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;

    public function __construct(ResourceBorrowRequest $request)
    {
        $this->request = $request;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Resource Borrow Request Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your borrowing request for ' . $this->request->resource->name . ' has been approved.')
            ->line('Quantity: ' . $this->request->quantity)
            ->line('Borrow Date: ' . $this->request->start_date->format('F d, Y H:i'))
            ->line('Return Date: ' . $this->request->end_date->format('F d, Y H:i'))
            ->action('View Request', route('resource-borrow-requests.show', $this->request))
            ->line('Please pick up the resource at the specified location on the borrow date.')
            ->line('Thank you for using CRRMS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Resource Borrow Request Approved',
            'message' => 'Your borrowing request for ' . $this->request->resource->name . ' has been approved.',
            'request_id' => $this->request->id,
            'resource_name' => $this->request->resource->name,
            'quantity' => $this->request->quantity,
            'start_date' => $this->request->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->request->end_date->format('Y-m-d H:i:s'),
            'type' => 'resource_borrow_request_approved',
        ];
    }
}
