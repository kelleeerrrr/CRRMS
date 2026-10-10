<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationIndex extends Component
{
    use WithPagination;

    public $filter = 'all';
    public $type = '';

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'type' => ['except' => ''],
    ];

    public function mount()
    {
        // Subscribe to Echo channel for real-time updates
        $this->dispatch('echo:subscribe', ['userId' => Auth::id()]);
    }

    #[On('notification-created')]
    public function onNotificationCreated($event)
    {
        // Refresh notifications when a new one is created
        $this->resetPage();
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->dispatch('notification-updated');
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->dispatch('notification-updated');
    }

    public function deleteNotification(Notification $notification)
    {
        $notification->delete();

        $this->dispatch('notification-updated');
    }

    #[Computed]
    public function notifications()
    {
        $query = Auth::user()->notifications();

        if ($this->filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filter === 'read') {
            $query->where('is_read', true);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    #[Computed]
    public function unreadCount()
    {
        return Auth::user()->notifications()->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.notification-index', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
