<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Redis\Streamable;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendRestored extends Streamable
{
    use Dispatchable, SerializesModels;

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
            name: 'send.restored',
            payload: serialize($this)
        );
    }
}
