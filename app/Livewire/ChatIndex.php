<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
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
        return $this->chatService->getUserConversations(Auth::user());
    }

    #[Computed]
    public function unreadCount()
    {
        return $this->chatService->getUnreadMessageCount(Auth::user());
    }

    #[Renderless]
    public function refresh()
    {
        // This method can be called to refresh the component
    }

    public function render()
    {
        return view('livewire.chat-index', [
            'chats' => $this->chats,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
