<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel; // Use this one!
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObrMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn(): array
    {
        // Change from PrivateChannel to Channel
        return [
            new Channel('office-network'),
        ];
    }
}