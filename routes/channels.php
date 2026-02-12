<?php

use Illuminate\Support\Facades\Broadcast;
use Namu\WireChat\Models\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel(
    'my-conversation.{conversationId}',
    function ($user, $conversationId) {
        $conversation = Conversation::find($conversationId);
        if ($conversation) {

            if ($user->belongsToConversation($conversation)) {
                return true;
            }
        }

        return false;
    },
);
