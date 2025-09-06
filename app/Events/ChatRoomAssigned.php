<?php

namespace App\Events;

use App\Models\ChatRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRoomAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatRoom;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatRoom $chatRoom)
    {
        $this->chatRoom = $chatRoom;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
            new PrivateChannel('user-notifications.' . $this->chatRoom->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'chat.assigned';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'chat_room_id' => $this->chatRoom->id,
            'admin' => [
                'id' => $this->chatRoom->admin->id,
                'name' => $this->chatRoom->admin->full_name,
            ],
            'customer' => [
                'id' => $this->chatRoom->customer->id,
                'name' => $this->chatRoom->customer->full_name,
            ],
            'subject' => $this->chatRoom->subject,
        ];
    }
}
