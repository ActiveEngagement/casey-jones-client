<?php

namespace Actengage\CaseyJones\Contracts;

use Actengage\CaseyJones\Redis\StreamPayload;

interface Streamable
{
    /**
     * Create the payload for the stream.
     *
     * @return StreamPayload
     */
    public function payload(): StreamPayload;

    /**
     * Dispatch the event to the Redis stream.
     *
     * @return void
     */
    public function dispatchToStream(): void;
}