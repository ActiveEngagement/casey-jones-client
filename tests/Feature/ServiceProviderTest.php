<?php

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Events\Dispatcher;
use Actengage\CaseyJones\Redis\Stream;
use Actengage\CaseyJones\Services\MessageGears;

it('registers the core singletons and aliases', function () {
    expect(app(Client::class))->toBeInstanceOf(Client::class)
        ->and(app('casey.client'))->toBe(app(Client::class))
        ->and(app(MessageGears::class))->toBeInstanceOf(MessageGears::class)
        ->and(app('casey.mg'))->toBe(app(MessageGears::class))
        ->and(app(Dispatcher::class))->toBeInstanceOf(Dispatcher::class)
        ->and(app('casey.events'))->toBe(app(Dispatcher::class));
});

it('resolves the redis stream singleton from the configured connection', function () {
    config()->set('database.redis.casey', [
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
    ]);

    expect(app(Stream::class))->toBeInstanceOf(Stream::class)
        ->and(app('casey.stream'))->toBe(app(Stream::class));
});
