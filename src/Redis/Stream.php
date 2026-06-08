<?php

namespace Actengage\CaseyJones\Redis;

use Illuminate\Redis\Connections\Connection;

class Stream
{
    /**
     * Construct the stream dispatch.
     */
    public function __construct(
        protected Connection $connection
    ) {
        //
    }

    /**
     * Add an event to the redis stream.
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
     * @param  string|array<int,string>  ...$keys
     */
    public function delete(string $token, string|array ...$keys): int
    {
        return $this->connection->xdel($token, collect($keys)->flatten()->all());
    }
}
