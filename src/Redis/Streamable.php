<?php

namespace Actengage\CaseyJones\Redis;

use Actengage\CaseyJones\Contracts\Streamable as StreamableInterface;
use Actengage\CaseyJones\Redis\StreamDispatcher;

abstract class Streamable implements StreamableInterface
{
    /**
     * Dispatch the event to the Redis stream.
     *
     * @return void
     */
    public function dispatchToStream(): void
    {
        app(StreamDispatcher::class)->dispatch($this->payload());
    }
}