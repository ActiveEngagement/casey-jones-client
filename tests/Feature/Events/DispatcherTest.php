<?php

use Actengage\CaseyJones\Events\Dispatcher;
use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Events\SendDeleted;
use Actengage\CaseyJones\Events\SendUpdated;
use Actengage\CaseyJones\Events\StreamEventReceived;
use Actengage\CaseyJones\Facades\StreamableEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Queue;

use function Illuminate\Events\queueable;

class EventsReceived implements ShouldQueue {            
    public static array $events = [];            
    public static array $wildcards = [];

    public function handle(StreamEventReceived $event) {
        EventsReceived::$events[] = get_class($event->payload);
    }
}

beforeEach(function() {
    EventsReceived::$events = [];
});

describe('event dispatcher', function() {
    it('is registered to the container', function() {
        expect(app(Dispatcher::class))->toBeInstanceOf(Dispatcher::class);
    });

    it('can listen for streamable events using an event listener', function() {
        StreamableEvent::listen(SendCreated::class, EventsReceived::class);

        StreamableEvent::listen([
            SendCreated::class,
            SendUpdated::class
        ], EventsReceived::class);

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        expect(EventsReceived::$events)->toBe([
            SendCreated::class,
            SendCreated::class,
            SendUpdated::class
        ]);
    });

    it('can listen for streamable events using closure', function() {
        StreamableEvent::listen(SendCreated::class, function(StreamEventReceived $event) {
            EventsReceived::$events[] = get_class($event->payload);
        });

        StreamableEvent::listen([
            SendCreated::class,
            SendUpdated::class
        ], function(StreamEventReceived $event) {
            EventsReceived::$events[] = get_class($event->payload);
        });

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        expect(EventsReceived::$events)->toBe([
            SendCreated::class,
            SendCreated::class,
            SendUpdated::class
        ]);
    });

    it('can listen for streamable events queueable using closure', function() {
        StreamableEvent::listen(SendCreated::class, queueable(function(StreamEventReceived $event) use (&$events) {
            EventsReceived::$events[] = get_class($event->payload);
        }));

        StreamableEvent::listen([
            SendCreated::class,
            SendUpdated::class
        ], queueable(function(StreamEventReceived $event) use (&$events) {
            EventsReceived::$events[] = get_class($event->payload);
        }));

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        expect(EventsReceived::$events)->toBe([
            SendCreated::class,
            SendCreated::class,
            SendUpdated::class
        ]);
    });

    it('dispatches the queueable closure properly', function() {
        Queue::fake();

        StreamableEvent::listen(SendCreated::class, queueable(function(StreamEventReceived $event) use (&$events) {
            EventsReceived::$events[] = get_class($event->payload);
        }));

        StreamableEvent::listen([
            SendCreated::class,
            SendUpdated::class
        ], queueable(function(StreamEventReceived $event) use (&$events) {
            EventsReceived::$events[] = get_class($event->payload);
        }));

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        expect(EventsReceived::$events)->toBe([]);

        Queue::assertPushed(CallQueuedListener::class, 3);
    });

    it('will catch unregistered streamable events', function() {
        StreamableEvent::catch(function(StreamEventReceived $event) {
            EventsReceived::$wildcards[] = get_class($event->payload);
        });

        StreamableEvent::listen(SendCreated::class, function(StreamEventReceived $event) {
            EventsReceived::$events[] = get_class($event->payload);
        });

        StreamableEvent::listen([
            SendCreated::class,
            SendUpdated::class
        ], function(StreamEventReceived $event) {
            EventsReceived::$events[] = get_class($event->payload);
        });

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        expect(EventsReceived::$events)->toBe([
            SendCreated::class,
            SendCreated::class,
            SendUpdated::class
        ]);
        
        expect(EventsReceived::$wildcards)->toBe([
            SendDeleted::class
        ]);
    });

    it('will catch and dispatch the queueable closure properly', function() {
        Queue::fake();

        StreamableEvent::catch(queueable(function(StreamEventReceived $event) {
            EventsReceived::$wildcards[] = get_class($event->payload);
        }));

        event(new StreamEventReceived('test', '1', new SendCreated('test', [])));
        event(new StreamEventReceived('test', '1', new SendUpdated('test', [])));
        event(new StreamEventReceived('test', '1', new SendDeleted('test', [])));

        Queue::assertPushed(CallQueuedListener::class, 3);
    });
});