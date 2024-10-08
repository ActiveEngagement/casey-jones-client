<?php

namespace Actengage\CaseyJones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Actengage\CaseyJones\Events\Dispatcher
 * @method static void listen(string|array<class-string<\Actengage\CaseyJones\Contracts\Streamable>> $events, \Illuminate\Events\QueuedClosure|\Closure|string|array $listener)
 * @method static void catch(\Illuminate\Events\QueuedClosure|\Closure)
 */
class StreamableEvent extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'casey.events';
    }
}