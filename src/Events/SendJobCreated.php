<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Redis\Streamable;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendJobCreated extends Streamable
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  array<string, mixed>  $sendJob
     */
    public function __construct(
        public string $token,
        public array $sendJob
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
            name: 'send-job.created',
            payload: serialize($this)
        );
    }
}
