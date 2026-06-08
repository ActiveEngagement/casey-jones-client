<?php

use Actengage\CaseyJones\Events\SendActiveTooLong;
use Actengage\CaseyJones\Events\SendCancelled;
use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Events\SendDeleted;
use Actengage\CaseyJones\Events\SendDelivered;
use Actengage\CaseyJones\Events\SendFailed;
use Actengage\CaseyJones\Events\SendJobCreated;
use Actengage\CaseyJones\Events\SendJobDeleted;
use Actengage\CaseyJones\Events\SendJobRestored;
use Actengage\CaseyJones\Events\SendJobTrashed;
use Actengage\CaseyJones\Events\SendJobUpdated;
use Actengage\CaseyJones\Events\SendRestored;
use Actengage\CaseyJones\Events\SendScheduled;
use Actengage\CaseyJones\Events\SendTrashed;
use Actengage\CaseyJones\Events\SendUpdated;
use Actengage\CaseyJones\Events\StreamEventReceived;
use Actengage\CaseyJones\Redis\Stream;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Broadcasting\PrivateChannel;

it('builds a stream payload with the expected name', function (string $class, string $name) {
    $event = new $class('the-token', ['id' => 1]);

    $payload = $event->payload();

    expect($payload)->toBeInstanceOf(StreamPayload::class)
        ->and($payload->token)->toBe('the-token')
        ->and($payload->name)->toBe($name)
        ->and($payload->payload)->toBe(serialize($event));
})->with([
    'active too long' => [SendActiveTooLong::class, 'send.active-too-long'],
    'cancelled' => [SendCancelled::class, 'send.cancelled'],
    'created' => [SendCreated::class, 'send.created'],
    'deleted' => [SendDeleted::class, 'send.deleted'],
    'delivered' => [SendDelivered::class, 'send.delivered'],
    'failed' => [SendFailed::class, 'send.failed'],
    'restored' => [SendRestored::class, 'send.restored'],
    'scheduled' => [SendScheduled::class, 'send.scheduled'],
    'trashed' => [SendTrashed::class, 'send.trashed'],
    'updated' => [SendUpdated::class, 'send.updated'],
    'job created' => [SendJobCreated::class, 'send-job.created'],
    'job deleted' => [SendJobDeleted::class, 'send-job.deleted'],
    'job restored' => [SendJobRestored::class, 'send-job.restored'],
    'job trashed' => [SendJobTrashed::class, 'send-job.trashed'],
    'job updated' => [SendJobUpdated::class, 'send-job.updated'],
]);

it('adds the event to the redis stream', function () {
    $stream = Mockery::mock(Stream::class);
    $stream->shouldReceive('add')->once()->with(Mockery::type(StreamPayload::class))->andReturn('1-0');

    app()->instance(Stream::class, $stream);

    (new SendCreated('the-token', ['id' => 1]))->addToStream();
});

describe('StreamEventReceived', function () {
    it('deletes itself from the stream', function () {
        $stream = Mockery::mock(Stream::class);
        $stream->shouldReceive('delete')->once()->with('the-token', '99-0')->andReturn(1);

        app()->instance(Stream::class, $stream);

        $event = new StreamEventReceived('the-token', '99-0', new SendCreated('the-token', []));

        expect($event->deleteFromStream())->toBe(1);
    });

    it('broadcasts on a private channel', function () {
        $event = new StreamEventReceived('the-token', '99-0', new SendCreated('the-token', []));

        expect($event->broadcastOn()[0])->toBeInstanceOf(PrivateChannel::class);
    });
});
