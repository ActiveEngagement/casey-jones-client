<?php

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Models\Send;
use Actengage\CaseyJones\Redis\Streamable;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendFailed extends Streamable
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $token,
        public Send $send
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
            name: 'send.failed',
            payload: serialize($this)
        );
    }
}
