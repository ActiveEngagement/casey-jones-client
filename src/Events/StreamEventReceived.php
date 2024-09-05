<?php

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Contracts\Streamable;
use Actengage\CaseyJones\Redis\Stream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamEventReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $app,
        public readonly string $key,
        public readonly Streamable $payload
    ) {
        //
    }

    /**
     * Remove this event from the Redis stream.
     *
     * @return int
     */
    public function deleteFromStream(): int
    {
        return app(Stream::class)->delete($this->app, $this->key);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
