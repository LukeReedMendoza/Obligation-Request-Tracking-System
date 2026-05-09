<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// IMPORTANT: You MUST add 'implements ShouldBroadcast' here!
class ObrMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        // We don't need to pass data, just the trigger!
    }

    public function broadcastOn(): array
    {
        // This is the radio frequency the whole office will tune into
        return [
            new Channel('office-network'),
        ];
    }
}