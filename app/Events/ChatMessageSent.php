<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $threadId,
        public Chat $message,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.thread.'.$this->threadId),
            new PrivateChannel('chat.inbox'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'thread_id' => $this->threadId,
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'message_type' => $this->message->message_type,
                'is_read' => (bool) $this->message->is_read,
                'created_at' => optional($this->message->created_at)->toISOString(),
            ],
        ];
    }
}
