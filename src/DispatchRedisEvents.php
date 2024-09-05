<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Events\StreamEventReceived;
use Illuminate\Support\Facades\Event;

class DispatchRedisEvents
{
    /**
     * Listen for Redis stream events and dispatch them on the client server.
     *
     * @return void
     */
    public static function listen(): void
    {
        Event::listen(StreamEventReceived::class, function(StreamEventReceived $event) {
            event($event->payload);
        });
    }
}