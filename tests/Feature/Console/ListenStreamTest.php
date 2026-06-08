<?php

use Actengage\CaseyJones\Console\ListenStream;
use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Events\StreamEventReceived;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Tester\CommandTester;

use function Pest\Laravel\artisan;

it('throws when no token can be resolved', function () {
    config()->set('casey.api_key', null);

    $command = app(ListenStream::class);
    $command->setLaravel(app());

    (new CommandTester($command))->execute([]);
})->throws(InvalidArgumentException::class, 'Invalid personal access token');

it('reads messages, dispatches stream events and stops on the restart signal', function () {
    Event::fake();

    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xread')->andReturn([
        'the-token' => [
            '1-0' => ['name' => 'send.created', 'payload' => serialize(new SendCreated('the-token', []))],
            '1-1' => ['name' => 'send.updated'],
        ],
    ]);

    Redis::shouldReceive('connection')->andReturn($connection);

    // First read snapshots the restart value, the periodic poll then sees it change.
    Cache::shouldReceive('get')->with('casey:restart')->andReturn('a', 'a', 'b');

    artisan('casey:listen', [
        '--token' => 'the-token',
        '--interval' => '0',
        '--poll' => '0.02',
        '--timeout' => '2',
    ])->assertExitCode(0);

    Event::assertDispatched(StreamEventReceived::class, 1);
});

it('hashes the configured api key, handles an empty stream and stops on timeout', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xread')->andReturn([]);

    Redis::shouldReceive('connection')->andReturn($connection);

    config()->set('casey.api_key', 'app-id|secret-key');

    artisan('casey:listen', [
        '--interval' => '0',
        '--timeout' => '0.2',
    ])->assertExitCode(0);
});

it('returns an empty result when the redis stream is not an array', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xread')->andReturn(false);

    Redis::shouldReceive('connection')->andReturn($connection);

    artisan('casey:listen', [
        '--token' => 'the-token',
        '--interval' => '0',
        '--timeout' => '0.2',
    ])->assertExitCode(0);
});
