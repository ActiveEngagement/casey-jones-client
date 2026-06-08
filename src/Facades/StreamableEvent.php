<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Facades;

use Actengage\CaseyJones\Events\Dispatcher;
use Illuminate\Support\Facades\Facade;

/**
 * @see Dispatcher
 *
 * @method static void listen(string|array<int, class-string<\Actengage\CaseyJones\Contracts\Streamable>> $events, \Illuminate\Events\QueuedClosure|\Closure|string|array<int, mixed>|null $listener = null)
 * @method static void catch(\Illuminate\Events\QueuedClosure|\Closure|string|array<int, mixed> $listener)
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
