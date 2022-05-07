<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReMarkableAuthenticatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	private string $authFileContents;

	/**
     * Create a new event instance.
     *
	 * @param string $authFileContents
	 * @return void
     */
    public function __construct(string $authFileContents)
    {
	    $this->authFileContents = $authFileContents;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
