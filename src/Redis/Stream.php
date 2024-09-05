<?php

namespace Actengage\CaseyJones\Redis;

use Illuminate\Redis\Connections\Connection;

class Stream
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
     * @return string
     */
    public function add(StreamPayload $payload): string
    {
        return $this->connection->xadd(
            $payload->token, $payload->id, $payload->toArray()
        );
    }

    /**
     * Dispatch an event to the redis stream.
     *
     * @param string $token
     * @param string|array<int,string> ...$keys
     * @return int
     */
    public function delete(string $token, string|array ...$keys): int
    {
        return $this->connection->xdel($token, collect($keys)->flatten()->all());
    }
}