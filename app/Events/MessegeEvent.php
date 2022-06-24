<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessegeEvent implements ShouldBroadcast
{
    use  InteractsWithSockets, SerializesModels;

    public array $messege;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($messege)
    {

        $this->messege  = $messege;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['messege'];
    }
}
