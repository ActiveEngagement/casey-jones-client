<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Redis;

use Actengage\CaseyJones\Contracts\Streamable as StreamableInterface;

abstract class Streamable implements StreamableInterface
{
    /**
     * The redis stream key.
     */
    public ?string $key = null;

    /**
     * Add the event to the Redis stream.
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
