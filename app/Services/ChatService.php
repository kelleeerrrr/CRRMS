<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatService
{
    public function createPrivateChat(array $data, User $user): Chat
    {
        return DB::transaction(function () use ($data, $user) {
            $chat = Chat::create([
                'id' => Str::uuid(),
                'type' => 'private',
                'name' => null,
                'created_by' => $user->id,
            ]);

            // Add participants
            $chat->participants()->attach([$user->id, $data['participant_id']]);

            // Create initial message if provided
            if (isset($data['message'])) {
                $this->sendMessage($chat, [
                    'message' => $data['message'],
                    'sender_id' => $user->id,
                ]);
            }

            Log::info('Private chat created', [
                'chat_id' => $chat->id,
                'creator_id' => $user->id,
                'participant_id' => $data['participant_id'],
            ]);

            return $chat;
        });
    }

    public function createGroupChat(array $data, User $user): Chat
    {
        return DB::transaction(function () use ($data, $user) {
            $chat = Chat::create([
                'id' => Str::uuid(),
                'type' => 'group',
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => $user->id,
            ]);

            // Add creator as admin
            $chat->participants()->attach($user->id, ['is_admin' => true]);

            // Add other participants
            if (isset($data['participants'])) {
                foreach ($data['participants'] as $participantId) {
                    $chat->participants()->attach($participantId);
                }
            }

            // Create initial message if provided
            if (isset($data['message'])) {
                $this->sendMessage($chat, [
                    'message' => $data['message'],
                    'sender_id' => $user->id,
                ]);
            }

            Log::info('Group chat created', [
                'chat_id' => $chat->id,
                'creator_id' => $user->id,
                'participants_count' => count($data['participants'] ?? []) + 1,
            ]);

            return $chat;
        });
    }

    public function sendMessage(Chat $chat, array $data): ChatMessage
    {
        return DB::transaction(function () use ($chat, $data) {
            $message = ChatMessage::create([
                'id' => Str::uuid(),
                'chat_id' => $chat->id,
                'sender_id' => $data['sender_id'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'text',
                'file_path' => $data['file_path'] ?? null,
                'file_name' => $data['file_name'] ?? null,
            ]);

            // Update last message in chat
            $chat->update(['last_message_at' => now()]);

            // Mark as seen by sender
            $message->seenBy()->attach($data['sender_id']);

            Log::info('Message sent', [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'sender_id' => $data['sender_id'],
            ]);

            return $message;
        });
    }

    public function markAsSeen(ChatMessage $message, User $user): void
    {
        DB::transaction(function () use ($message, $user) {
            if (!$message->seenBy()->where('user_id', $user->id)->exists()) {
                $message->seenBy()->attach($user->id, ['seen_at' => now()]);

                Log::info('Message marked as seen', [
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }

    public function markTyping(Chat $chat, User $user, bool $isTyping): void
    {
        // This would typically use Redis or broadcasting for real-time
        // For now, we'll store in cache
        $cacheKey = "chat:{$chat->id}:typing:{$user->id}";
        
        if ($isTyping) {
            cache()->put($cacheKey, true, now()->addSeconds(10));
        } else {
            cache()->forget($cacheKey);
        }
    }

    public function getTypingUsers(Chat $chat): array
    {
        $typingUsers = [];
        
        foreach ($chat->participants as $participant) {
            $cacheKey = "chat:{$chat->id}:typing:{$participant->id}";
            if (cache()->has($cacheKey)) {
                $typingUsers[] = $participant;
            }
        }
        
        return $typingUsers;
    }

    public function addParticipant(Chat $chat, User $user, User $addedBy): void
    {
        DB::transaction(function () use ($chat, $user, $addedBy) {
            if (!$chat->participants()->where('user_id', $user->id)->exists()) {
                $chat->participants()->attach($user->id);

                // System message about new participant
                $this->sendMessage($chat, [
                    'message' => "{$addedBy->name} added {$user->name} to the chat",
                    'sender_id' => $addedBy->id,
                    'type' => 'system',
                ]);

                Log::info('Participant added to chat', [
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'added_by' => $addedBy->id,
                ]);
            }
        });
    }

    public function removeParticipant(Chat $chat, User $user, User $removedBy): void
    {
        DB::transaction(function () use ($chat, $user, $removedBy) {
            if ($chat->participants()->where('user_id', $user->id)->exists()) {
                $chat->participants()->detach($user->id);

                // System message about removed participant
                $this->sendMessage($chat, [
                    'message' => "{$removedBy->name} removed {$user->name} from the chat",
                    'sender_id' => $removedBy->id,
                    'type' => 'system',
                ]);

                Log::info('Participant removed from chat', [
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'removed_by' => $removedBy->id,
                ]);
            }
        });
    }

    public function updateChat(Chat $chat, array $data): Chat
    {
        return DB::transaction(function () use ($chat, $data) {
            $chat->update($data);

            Log::info('Chat updated', [
                'chat_id' => $chat->id,
            ]);

            return $chat->fresh();
        });
    }

    public function deleteChat(Chat $chat): bool
    {
        return DB::transaction(function () use ($chat) {
            $chat->messages()->delete();
            $chat->participants()->detach();
            $chat->delete();

            Log::info('Chat deleted', [
                'chat_id' => $chat->id,
            ]);

            return true;
        });
    }

    public function getUserChats(User $user, $filters = [])
    {
        $query = $chat = Chat::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['participants', 'lastMessage', 'lastMessage.sender']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('last_message_at', 'desc')->get();
    }

    public function getChatMessages(Chat $chat, $limit = 50)
    {
        return $chat->messages()
            ->with(['sender', 'seenBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }

    public function getUnreadMessageCount(User $user): int
    {
        return ChatMessage::whereHas('chat', function ($q) use ($user) {
            $q->whereHas('participants', function ($subQ) use ($user) {
                $subQ->where('user_id', $user->id);
            });
        })
        ->where('sender_id', '!=', $user->id)
        ->whereDoesntHave('seenBy', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->count();
    }
}
