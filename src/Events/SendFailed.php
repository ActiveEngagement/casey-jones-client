<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Redis\Streamable;
use Actengage\CaseyJones\Redis\StreamPayload;

class SendFailed extends Streamable
{
    /**
     * Create a new event instance.
     *
     * @param  array<string, mixed>  $send
     */
    public function __construct(
        public string $token,
        public array $send
    ) {
        //
    }

    /**
     * Define the Redis stream payload.
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
