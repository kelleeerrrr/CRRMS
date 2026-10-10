<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChat;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        // Chat feature not yet implemented - models don't exist
        return view('chat.index', [
            'chats' => collect(),
            'unreadCount' => 0
        ]);
    }

    public function create()
    {
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();

        return view('chat.create', compact('users'));
    }

    public function store(StoreChat $request)
    {
        try {
            if ($request->type === 'private') {
                $chat = $this->chatService->createPrivateChat(
                    $request->validated(),
                    Auth::user()
                );
            } else {
                $chat = $this->chatService->createGroupChat(
                    $request->validated(),
                    Auth::user()
                );
            }

            return redirect()
                ->route('chat.show', $chat)
                ->with('success', 'Chat created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);

        $chat->load(['participants', 'messages' => function ($q) {
            $q->with(['sender', 'seenBy'])->orderBy('created_at', 'desc')->limit(50);
        }]);

        $messages = $chat->messages->reverse();
        $typingUsers = $this->chatService->getTypingUsers($chat);

        return view('chat.show', compact('chat', 'messages', 'typingUsers'));
    }

    public function edit(Chat $chat)
    {
        $this->authorize('update', $chat);

        $chat->load('participants');
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();

        return view('chat.edit', compact('chat', 'users'));
    }

    public function update(Request $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $chat = $this->chatService->updateChat($chat, $request->validated());

            return redirect()
                ->route('chat.show', $chat)
                ->with('success', 'Chat updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Chat $chat)
    {
        $this->authorize('delete', $chat);

        try {
            $this->chatService->deleteChat($chat);

            return redirect()
                ->route('chat.index')
                ->with('success', 'Chat deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorize('view', $chat);

        $request->validate([
            'message' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB max
        ]);

        try {
            $data = [
                'message' => $request->message,
                'sender_id' => Auth::id(),
                'type' => 'text',
            ];

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('chat-files', 'public');
                $data['file_path'] = $path;
                $data['file_name'] = $file->getClientOriginalName();
                $data['type'] = 'file';
            }

            $message = $this->chatService->sendMessage($chat, $data);

            return back();
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function markAsSeen(ChatMessage $message)
    {
        $this->authorize('view', $message->chat);

        try {
            $this->chatService->markAsSeen($message, Auth::user());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function markTyping(Request $request, Chat $chat)
    {
        $this->authorize('view', $chat);

        $request->validate([
            'is_typing' => ['required', 'boolean'],
        ]);

        $this->chatService->markTyping($chat, Auth::user(), $request->is_typing);

        return response()->json(['success' => true]);
    }

    public function addParticipant(Request $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->user_id);
            $this->chatService->addParticipant($chat, $user, Auth::user());

            return back()
                ->with('success', 'Participant added successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function removeParticipant(Request $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->user_id);
            $this->chatService->removeParticipant($chat, $user, Auth::user());

            return back()
                ->with('success', 'Participant removed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
