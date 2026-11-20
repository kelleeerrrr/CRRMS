<?php

namespace App\Notifications;

use App\Models\ResourceBorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceBorrowRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $reason;

    public function __construct(ResourceBorrowRequest $request, $reason = null)
    {
        $this->request = $request;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Resource Borrow Request Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your borrowing request for ' . $this->request->resource->name . ' has been rejected.')
            ->line('Quantity: ' . $this->request->quantity)
            ->line('Requested Dates: ' . $this->request->start_date->format('F d, Y H:i') . ' to ' . $this->request->end_date->format('F d, Y H:i'));

        if ($this->reason) {
            $mailMessage->line('Reason: ' . $this->reason);
        }

        $mailMessage->action('View Request', route('resource-borrow-requests.show', $this->request))
            ->line('You may submit a new request with different dates or quantities.')
            ->line('Thank you for using CRRMS!');

        return $mailMessage;
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Resource Borrow Request Rejected',
            'message' => 'Your borrowing request for ' . $this->request->resource->name . ' has been rejected.',
            'request_id' => $this->request->id,
            'resource_name' => $this->request->resource->name,
            'quantity' => $this->request->quantity,
            'rejection_reason' => $this->reason,
            'type' => 'resource_borrow_request_rejected',
        ];
    }
}
