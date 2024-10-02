<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Events\StreamEventReceived;
use Actengage\CaseyJones\Listeners\SendWasCreated;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class DispatchRedisEvents
{
    /**
     * Listen for Redis stream events and dispatch them on the client server.
     *
     * @param array<class-string<\Actengage\CaseyJones\Contracts\Streamable>,array|class-string>> $events
     * @return void
     */
    public static function listen(array $events = []): void
    {
        Event::listen(StreamEventReceived::class, function(StreamEventReceived $event) use ($events) {
            $listeners = collect(Arr::get($events, get_class($event->payload)));

            foreach($listeners as $listener) {
                (new $listener)->handle($event);
            }
        });
    }
}