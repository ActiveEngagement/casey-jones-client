<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Contracts;

use Actengage\CaseyJones\Redis\StreamPayload;

interface Streamable
{
    /**
     * Create the payload for the stream.
     */
    public function payload(): StreamPayload;

    /**
     * Add the event to the Redis stream.
     */
    public function addToStream(): void;

    // /**
    //  * Delete the event from the Redis stream.
    //  *
    //  * @return void
    //  */
    // public function deleteFromStream(): void;
}
