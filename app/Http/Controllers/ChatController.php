<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChat;
use App\Models\Conversation;
use App\Models\Message;
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
        $conversations = $this->chatService->getUserConversations(Auth::user());
        $unreadCount = $this->chatService->getUnreadMessageCount(Auth::user());

        return view('chat.index', [
            'chats' => $conversations,
            'unreadCount' => $unreadCount
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
                $conversation = $this->chatService->createPrivateChat(
                    $request->validated(),
                    Auth::user()
                );
            } else {
                $conversation = $this->chatService->createGroupChat(
                    $request->validated(),
                    Auth::user()
                );
            }

            return redirect()
                ->route('chat.show', $conversation)
                ->with('success', 'Chat created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Conversation $conversation)
    {
        // Temporarily bypass authorization for debugging
        // $this->authorize('view', $conversation);

        // Debug: Check if user is participant
        $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
        
        if (!$isParticipant) {
            return back()->with('error', 'You are not a participant in this conversation. Your ID: ' . Auth::id() . ', Participants: ' . $conversation->participants->pluck('user_id')->implode(', '));
        }

        $conversation->load(['participants', 'facility', 'equipment', 'resource', 'facilityReservation', 'equipmentBorrowRequest', 'resourceBorrowRequest', 'messages' => function ($q) {
            $q->with(['sender'])->orderBy('created_at', 'desc')->limit(50);
        }]);

        $messages = $conversation->messages->reverse();
        $typingUsers = $this->chatService->getTypingUsers($conversation);

        // Mark conversation as read
        $this->chatService->markConversationAsRead($conversation, Auth::user());

        return view('chat.show', compact('conversation', 'messages', 'typingUsers'));
    }

    public function edit(Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $conversation->load('participants');
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();

        return view('chat.edit', compact('conversation', 'users'));
    }

    public function update(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $conversation = $this->chatService->updateConversation($conversation, $request->validated());

            return redirect()
                ->route('chat.show', $conversation)
                ->with('success', 'Chat updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        try {
            $this->chatService->deleteConversation($conversation);

            return redirect()
                ->route('chat.index')
                ->with('success', 'Chat deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        // Temporarily bypass authorization for debugging
        // $this->authorize('view', $conversation);

        // Check if user is participant
        $isParticipant = $conversation->participants()->where('user_id', Auth::id())->exists();
        
        if (!$isParticipant) {
            \Log::error('User not participant in conversation', [
                'user_id' => Auth::id(),
                'conversation_id' => $conversation->id,
                'participants' => $conversation->participants->pluck('user_id')->toArray()
            ]);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'You are not a participant in this conversation.'], 403);
            }
            return back()->with('error', 'You are not a participant in this conversation.');
        }

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

            \Log::info('Attempting to send message', [
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => $request->message,
            ]);

            $message = $this->chatService->sendMessage($conversation, $data);

            \Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'content' => $message->content,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return back()->with('success', 'Message sent!');
        } catch (\Exception $e) {
            \Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function markAsSeen(Message $message)
    {
        $this->authorize('view', $message->conversation);

        try {
            $this->chatService->markAsSeen($message, Auth::user());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function markTyping(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $request->validate([
            'is_typing' => ['required', 'boolean'],
        ]);

        $this->chatService->markTyping($conversation, Auth::user(), $request->is_typing);

        return response()->json(['success' => true]);
    }

    public function addParticipant(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->user_id);
            $this->chatService->addParticipant($conversation, $user, Auth::user());

            return back()
                ->with('success', 'Participant added successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function removeParticipant(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $user = \App\Models\User::findOrFail($request->user_id);
            $this->chatService->removeParticipant($conversation, $user, Auth::user());

            return back()
                ->with('success', 'Participant removed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function chatWithOwner(Request $request)
    {
        $request->validate([
            'resource_type' => ['required', 'in:facility,equipment,resource'],
            'resource_id' => ['required', 'uuid'],
            'transaction_id' => ['nullable', 'uuid'],
            'transaction_type' => ['nullable', 'in:facility_reservation,equipment_borrow_request,resource_borrow_request'],
        ]);

        try {
            $resource = match($request->resource_type) {
                'facility' => \App\Models\Facility::findOrFail($request->resource_id),
                'equipment' => \App\Models\Equipment::findOrFail($request->resource_id),
                'resource' => \App\Models\Resource::findOrFail($request->resource_id),
            };

            $owner = $resource->owner ?? \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'school_officer');
            })->first();

            if (!$owner) {
                return back()->with('error', 'No owner found for this resource.');
            }

            if ($owner->id === Auth::id()) {
                return back()->with('error', 'You cannot chat with yourself.');
            }

            $conversation = $this->chatService->findOrCreateResourceChat(
                $request->resource_type,
                $request->resource_id,
                Auth::user(),
                $owner,
                $request->transaction_id,
                $request->transaction_type
            );

            // Refresh to ensure participants are loaded
            $conversation->refresh();

            // Debug: Log participants
            \Log::info('Chat with owner - Participants', [
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'owner_id' => $owner->id,
                'participants' => $conversation->participants->pluck('user_id')->toArray(),
            ]);

            // Verify user is a participant
            if (!$conversation->participants()->where('user_id', Auth::id())->exists()) {
                return back()->with('error', 'Failed to add you to the conversation.');
            }

            return redirect()->route('chat.show', $conversation);
        } catch (\Exception $e) {
            \Log::error('Chat with owner error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }
}
