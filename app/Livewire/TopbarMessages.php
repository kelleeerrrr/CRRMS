<?php

namespace App\Livewire;

use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class TopbarMessages extends Component
{
    public function render(ChatService $chat): View
    {
        $user = Auth::user();

        return view('livewire.topbar-messages', [
            'recentMessages' => $chat->getLatestIncomingMessagesBySender($user),
            'unreadCount' => $chat->getUnreadMessageCount($user),
        ]);
    }
}
