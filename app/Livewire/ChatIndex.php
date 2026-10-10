<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ChatIndex extends Component
{
    public $search = '';
    public $filter = 'all';

    protected ChatService $chatService;

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    #[Computed]
    public function chats()
    {
        // Chat feature not yet implemented - models don't exist
        return collect();
    }

    #[Computed]
    public function unreadCount()
    {
        // Chat feature not yet implemented - models don't exist
        return 0;
    }

    public function render()
    {
        return view('livewire.chat-index', [
            'chats' => $this->chats,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
