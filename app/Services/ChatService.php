<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatService
{
    public function createPrivateChat(array $data, User $user): Conversation
    {
        return DB::transaction(function () use ($data, $user) {
            $conversation = Conversation::create([
                'id' => Str::uuid(),
                'type' => 'private',
                'name' => null,
                'created_by' => $user->id,
                'resource_type' => $data['resource_type'] ?? null,
                'resource_id' => $data['resource_id'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'transaction_type' => $data['transaction_type'] ?? null,
            ]);

            // Add participants
            $conversation->participants()->attach([
                $user->id => ['id' => Str::uuid()],
                $data['participant_id'] => ['id' => Str::uuid()],
            ]);

            // Create initial message if provided
            if (isset($data['message'])) {
                $this->sendMessage($conversation, [
                    'message' => $data['message'],
                    'sender_id' => $user->id,
                ]);
            }

            Log::info('Private chat created', [
                'conversation_id' => $conversation->id,
                'creator_id' => $user->id,
                'participant_id' => $data['participant_id'],
                'resource_type' => $data['resource_type'] ?? null,
                'resource_id' => $data['resource_id'] ?? null,
            ]);

            return $conversation;
        });
    }

    public function findOrCreateResourceChat(string $resourceType, string $resourceId, User $user, User $owner, ?string $transactionId = null, ?string $transactionType = null): Conversation
    {
        // Check if conversation already exists for this resource and participants
        $existingConversation = Conversation::where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($owner) {
                $q->where('user_id', $owner->id);
            })
            ->where('type', 'private')
            ->first();

        if ($existingConversation) {
            // Update transaction if provided and different
            if ($transactionId && $transactionType && (!$existingConversation->transaction_id || $existingConversation->transaction_id !== $transactionId)) {
                $existingConversation->update([
                    'transaction_id' => $transactionId,
                    'transaction_type' => $transactionType,
                ]);
            }
            return $existingConversation;
        }

        // Create new conversation
        return $this->createPrivateChat([
            'participant_id' => $owner->id,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'transaction_id' => $transactionId,
            'transaction_type' => $transactionType,
        ], $user);
    }

    public function createGroupChat(array $data, User $user): Conversation
    {
        return DB::transaction(function () use ($data, $user) {
            $conversation = Conversation::create([
                'id' => Str::uuid(),
                'type' => 'group',
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => $user->id,
            ]);

            // Add creator as admin
            $conversation->participants()->attach($user->id, ['id' => Str::uuid(), 'role' => 'admin']);

            // Add other participants
            if (isset($data['participants'])) {
                foreach ($data['participants'] as $participantId) {
                    $conversation->participants()->attach($participantId, ['id' => Str::uuid()]);
                }
            }

            // Create initial message if provided
            if (isset($data['message'])) {
                $this->sendMessage($conversation, [
                    'message' => $data['message'],
                    'sender_id' => $user->id,
                ]);
            }

            Log::info('Group chat created', [
                'conversation_id' => $conversation->id,
                'creator_id' => $user->id,
                'participants_count' => count($data['participants'] ?? []) + 1,
            ]);

            return $conversation;
        });
    }

    public function sendMessage(Conversation $conversation, array $data): Message
    {
        return DB::transaction(function () use ($conversation, $data) {
            $messageData = [
                'id' => Str::uuid(),
                'conversation_id' => $conversation->id,
                'sender_id' => $data['sender_id'],
                'type' => $data['type'] ?? 'text',
            ];

            // Handle content from 'message' key (what controller sends)
            if (isset($data['message'])) {
                $messageData['content'] = $data['message'];
            }

            // Handle file attachments
            if (isset($data['file_path'])) {
                $messageData['file_path'] = $data['file_path'];
                $messageData['file_name'] = $data['file_name'] ?? null;
            }

            $message = Message::create($messageData);

            Log::info('Message sent', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => $data['sender_id'],
                'content' => $message->content,
            ]);

            // Broadcast the message - wrap in try-catch to prevent transaction rollback
            try {
                broadcast(new MessageSent($message));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
                // Don't throw - message is already saved
            }

            return $message;
        });
    }

    public function markAsSeen(Message $message, User $user): void
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

    public function markConversationAsRead(Conversation $conversation, User $user): void
    {
        DB::transaction(function () use ($conversation, $user) {
            $participant = $conversation->participants()->where('user_id', $user->id)->first();
            if ($participant) {
                $conversation->participants()->updateExistingPivot($user->id, [
                    'last_read_at' => now(),
                ]);

                Log::info('Conversation marked as read', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }

    public function markTyping(Conversation $conversation, User $user, bool $isTyping): void
    {
        $cacheKey = "conversation:{$conversation->id}:typing:{$user->id}";
        
        if ($isTyping) {
            cache()->put($cacheKey, true, now()->addSeconds(10));
        } else {
            cache()->forget($cacheKey);
        }
    }

    public function getTypingUsers(Conversation $conversation): array
    {
        $typingUsers = [];
        
        foreach ($conversation->participants as $participant) {
            $cacheKey = "conversation:{$conversation->id}:typing:{$participant->id}";
            if (cache()->has($cacheKey)) {
                $typingUsers[] = $participant;
            }
        }
        
        return $typingUsers;
    }

    public function addParticipant(Conversation $conversation, User $user, User $addedBy): void
    {
        DB::transaction(function () use ($conversation, $user, $addedBy) {
            if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
                $conversation->participants()->attach($user->id, ['id' => Str::uuid()]);

                // System message about new participant
                $this->sendMessage($conversation, [
                    'message' => "{$addedBy->name} added {$user->name} to the chat",
                    'sender_id' => $addedBy->id,
                    'type' => 'system',
                ]);

                Log::info('Participant added to conversation', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'added_by' => $addedBy->id,
                ]);
            }
        });
    }

    public function removeParticipant(Conversation $conversation, User $user, User $removedBy): void
    {
        DB::transaction(function () use ($conversation, $user, $removedBy) {
            if ($conversation->participants()->where('user_id', $user->id)->exists()) {
                $conversation->participants()->detach($user->id);

                // System message about removed participant
                $this->sendMessage($conversation, [
                    'message' => "{$removedBy->name} removed {$user->name} from the chat",
                    'sender_id' => $removedBy->id,
                    'type' => 'system',
                ]);

                Log::info('Participant removed from conversation', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'removed_by' => $removedBy->id,
                ]);
            }
        });
    }

    public function updateConversation(Conversation $conversation, array $data): Conversation
    {
        return DB::transaction(function () use ($conversation, $data) {
            $conversation->update($data);

            Log::info('Conversation updated', [
                'conversation_id' => $conversation->id,
            ]);

            return $conversation->fresh();
        });
    }

    public function deleteConversation(Conversation $conversation): bool
    {
        return DB::transaction(function () use ($conversation) {
            $conversation->messages()->delete();
            $conversation->participants()->detach();
            $conversation->delete();

            Log::info('Conversation deleted', [
                'conversation_id' => $conversation->id,
            ]);

            return true;
        });
    }

    public function getUserConversations(User $user, $filters = [])
    {
        $query = Conversation::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['participants', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }, 'facility', 'equipment', 'resource']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $conversations = $query->latest()->get();

        // Load participant pivot data for current user
        foreach ($conversations as $conversation) {
            $conversation->current_participant = $conversation->participants->where('id', $user->id)->first();
        }

        return $conversations;
    }

    public function getConversationMessages(Conversation $conversation, $limit =  50)
    {
        return $conversation->messages()
            ->with(['sender'])
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse();
    }

    public function getLatestIncomingMessagesBySender(User $user): Collection
    {
        $rankedMessages = $this->incomingMessages($user)
            ->select('messages.id')
            ->selectRaw('ROW_NUMBER() OVER (
                PARTITION BY messages.sender_id
                ORDER BY messages.created_at DESC, messages.id DESC
            ) AS sender_rank');

        return Message::query()
            ->select('messages.*')
            ->joinSub($rankedMessages, 'ranked_messages', 'ranked_messages.id', '=', 'messages.id')
            ->where('ranked_messages.sender_rank', 1)
            ->with(['sender:id,name', 'conversation:id,name'])
            ->latest('messages.created_at')
            ->orderByDesc('messages.id')
            ->limit(5)
            ->get();
    }

    public function getUnreadMessageCount(User $user): int
    {
        return $this->incomingMessages($user)
            ->where(function (Builder $query) {
                $query->whereNull('conversation_participants.last_read_at')
                    ->orWhereColumn('conversation_participants.last_read_at', '<', 'messages.created_at');
            })
            ->distinct('messages.conversation_id')
            ->count('messages.conversation_id');
    }

    private function incomingMessages(User $user): Builder
    {
        return Message::query()
            ->select('messages.*')
            ->join('conversation_participants', 'conversation_participants.conversation_id', '=', 'messages.conversation_id')
            ->where('conversation_participants.user_id', $user->id)
            ->where('messages.sender_id', '!=', $user->id)
            ->whereNull('conversation_participants.left_at')
            ->where('messages.is_deleted', false);
    }
}
