<?php

namespace Actengage\CaseyJones\Redis;

use Actengage\CaseyJones\Contracts\Streamable as StreamableInterface;
use Actengage\CaseyJones\Redis\Stream;

abstract class Streamable implements StreamableInterface
{
    /**
     * Add the event to the Redis stream.
     *
     * @return void
     */
    public function addToStream(): void
    {
        app(Stream::class)->add($this->payload());
    }

    // /**
    //  * Delete the event from the Redis stream.
    //  *
    //  * @return void
    //  */
    // public function deleteFromStream(): void
    // {
    //     app(Stream::class)->delete($this->payload()->key);
    // }
}