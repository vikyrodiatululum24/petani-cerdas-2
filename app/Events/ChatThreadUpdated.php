<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatThreadUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Chat $thread,
        public string $action,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.thread.'.$this->thread->id),
            new PrivateChannel('chat.inbox'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.thread.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'thread_id' => $this->thread->id,
            'status' => $this->thread->status,
            'assigned_mentor_id' => $this->thread->assigned_mentor_id,
            'action' => $this->action,
        ];
    }
}
