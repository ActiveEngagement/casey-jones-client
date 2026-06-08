<?php

use Actengage\CaseyJones\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;

class ExposedDispatcher extends Dispatcher
{
    public function exposeResolveQueue(): QueueFactoryContract
    {
        return $this->resolveQueue();
    }
}

it('throws when the queue resolver has not been set', function () {
    (new ExposedDispatcher)->exposeResolveQueue();
})->throws(RuntimeException::class, 'The queue resolver has not been set.');
