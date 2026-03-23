<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.inbox', function ($user) {
    return $user->hasRole('admin') || $user->hasRole('mentor') || $user->hasRole('petani');
});

Broadcast::channel('chat.thread.{threadId}', function ($user, $threadId) {
    $thread = Chat::query()->whereNull('thread_id')->find($threadId);

    if (! $thread) {
        return false;
    }

    if ($user->hasRole('admin') || $user->hasRole('mentor')) {
        return true;
    }

    return (int) $thread->sender_id === (int) $user->id || (int) $thread->receiver_id === (int) $user->id;
});
