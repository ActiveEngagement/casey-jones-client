<?php

namespace Actengage\CaseyJones\Events;

use Actengage\CaseyJones\Contracts\Streamable;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\Events\QueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class Dispatcher extends EventsDispatcher
{
    /**
     * The streamable events with listeners
     *
     * @var Collection<int, class-string<Streamable>>
     */
    protected Collection $events;

    /**
     * The stream event wildcard events.
     *
     * @var array<int, QueuedClosure|Closure>
     */
    protected array $streamEventWildcards = [];

    /**
     * The queue resolver instance.
     *
     * @var (callable(): QueueFactoryContract)|null
     */
    protected $streamQueueResolver;

    /**
     * Create a new event dispatcher instance.
     */
    public function __construct(?ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;
        $this->events = $this->newEventCollection();
    }

    /**
     * Create a new empty collection of streamable events.
     *
     * @return Collection<int, class-string<Streamable>>
     */
    protected function newEventCollection(): Collection
    {
        return new Collection;
    }

    /**
     * Set the queue resolver implementation.
     *
     * @param  callable(): QueueFactoryContract  $resolver
     * @return $this
     */
    #[\Override]
    public function setQueueResolver(callable $resolver)
    {
        $this->streamQueueResolver = $resolver;

        return $this;
    }

    /**
     * Get the queue implementation from the resolver.
     */
    #[\Override]
    protected function resolveQueue(): QueueFactoryContract
    {
        if ($this->streamQueueResolver === null) {
            throw new \RuntimeException('The queue resolver has not been set.');
        }

        return ($this->streamQueueResolver)();
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  class-string<Streamable>|array<int, class-string<Streamable>>  $events
     * @param  QueuedClosure|Closure|string|array{0: class-string, 1: string}  $listener
     */
    #[\Override]
    public function listen($events, $listener = null): void
    {
        foreach (Collection::wrap($events) as $event) {
            if (! $this->events->contains($event)) {
                $this->events->push($event);
            }
        }

        Event::listen(StreamEventReceived::class, function (StreamEventReceived $event) use ($events, $listener) {
            if ($listener !== null && $this->shouldDispatchStreamableEvent($event, $events)) {
                if ($listener instanceof QueuedClosure) {
                    $this->makeListener($listener->resolve())($event, [$event]);
                } else {
                    $this->makeListener($listener)($event, [$event]);
                }
            }
        });
    }

    /**
     * Catch all the unregistered streamable events.
     *
     * @param  QueuedClosure|Closure|string|array{0: class-string, 1: string}  $listener
     */
    public function catch(QueuedClosure|Closure|string|array $listener): void
    {
        Event::listen(StreamEventReceived::class, function (StreamEventReceived $event) use ($listener) {
            if (! $this->events->contains($event->payload::class)) {
                if ($listener instanceof QueuedClosure) {
                    $this->makeListener($listener->resolve())($event, [$event]);
                } else {
                    $this->makeListener($listener)($event, [$event]);
                }
            }
        });
    }

    /**
     * Determines if the streamable event should dispatch on the listener.
     *
     * @param  class-string<Streamable>|array<int, class-string<Streamable>>  $events
     */
    protected function shouldDispatchStreamableEvent(StreamEventReceived $event, string|array $events): bool
    {
        $matches = $event->payload::class;

        return Collection::wrap($events)->contains(fn ($value) => $value === $matches) && $this->events->contains($matches);
    }
}
