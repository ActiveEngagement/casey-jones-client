<?php

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Redis\Streamable;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendCreated extends Streamable
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $token,
        public array $send
    ) {
        //
    }

    /**
     * Define the Redis stream payload.
     *
     * @return void
     */
    public function payload(): StreamPayload
    {
        return new StreamPayload(
            token: $this->token,
            name: 'send.created',
            payload: serialize($this)
        );
    }
}
