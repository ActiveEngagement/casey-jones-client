<?php

namespace Actengage\CaseyJones\Events;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\Events\QueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class Dispatcher extends EventsDispatcher
{
    /**
     * The streamable events with listeners
     *
     * @var Collection<int,class-string<\Actengage\CaseyJones\Contracts\Streamable>>
     */
    protected Collection $events;

    /**
     * The stream event wildcard events.
     *
     * @var Collection<int,QueuedClosure|Closure>
     */
    protected array $streamEventWildcards;

    /**
     * Create a new event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(?ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;
        $this->events = collect();
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string|array  $events
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string|array  $listener
     * @return void
     */
    public function listen($events, $listener = null): void
    {
        foreach(collect($events) as $event) {
            if(!$this->events->contains($event)) {
                $this->events->push($event);
            }
        }

        Event::listen(StreamEventReceived::class, function(StreamEventReceived $event) use ($events, $listener) {
            if($this->shouldDispatchStreamableEvent($event, $events)) {
                if($listener instanceof QueuedClosure) {
                    $this->makeListener($listener->resolve())($event, [$event]);
                }
                else {
                    $this->makeListener($listener)($event, [$event]);
                }
            }
        });
    }

    /**
     * Catch all the unregistered streamable events.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string|array  $listener
     * @return void
     */
    public function catch(QueuedClosure|Closure|string|array $listener): void
    {
        Event::listen(StreamEventReceived::class, function(StreamEventReceived $event) use ($listener) {
            if(! $this->events->contains(get_class($event->payload))) {
                if($listener instanceof QueuedClosure) {
                    $this->makeListener($listener->resolve())($event, [$event]);
                }
                else {
                    $this->makeListener($listener)($event, [$event]);
                }
            }
        });
    }

    /**
     * Determines if the streamable event should dispatch on the listener.
     *
     * @param  \Actengage\CaseyJones\Events\StreamEventReceived $event
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string|array  $events
     * @return bool
     */
    protected function shouldDispatchStreamableEvent(StreamEventReceived $event, string|array $events): bool
    {
        $matches = get_class($event->payload);

        return collect($events)->contains(function ($value) use ($matches) {
            return $value === $matches;
        }) && $this->events->contains($matches);
    }
}