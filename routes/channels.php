<?php

use Illuminate\Support\Facades\Broadcast;

// Legacy Laravel channel (for compatibility)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// CRRMS private user channel - users can only subscribe to their own channel
Broadcast::channel('users.{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});
