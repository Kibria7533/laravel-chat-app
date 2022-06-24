<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ConversationEvent implements ShouldBroadcast
{
    use  InteractsWithSockets, SerializesModels;

    public array $conversation;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversation)
    {

        $this->conversation  = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['conversation'];
    }
}
