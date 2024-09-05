<?php

namespace Actengage\CaseyJones\Redis;

use Illuminate\Redis\Connections\Connection;

class StreamDispatcher
{
    /**
     * Construct the stream dispatch.
     *
     * @param Connection $connection
     */
    public function __construct(
        protected Connection $connection
    ) {
        //
    }

    /**
     * Dispatch an event to the redis stream.
     *
     * @param StreamPayload $payload
     * @return void
     */
    public function dispatch(StreamPayload $payload): void
    {
        $this->connection->xadd(
            $payload->token, $payload->id, $payload->toArray()
        );
    }
}