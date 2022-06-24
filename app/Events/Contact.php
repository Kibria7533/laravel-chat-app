<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Contact implements ShouldBroadcast
{
    use  InteractsWithSockets, SerializesModels;

    public array $contact;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contact)
    {

        $this->contact  = $contact;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['contact'];
    }
}
