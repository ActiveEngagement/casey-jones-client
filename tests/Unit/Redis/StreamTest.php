<?php

use Actengage\CaseyJones\Redis\Stream;
use Actengage\CaseyJones\Redis\StreamPayload;
use Illuminate\Redis\Connections\Connection;

it('adds a payload to the stream', function () {
    $payload = new StreamPayload('the-token', 'send.created', 'serialized');

    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xadd')
        ->once()
        ->with('the-token', '*', $payload->toArray())
        ->andReturn('1526919030474-55');

    expect((new Stream($connection))->add($payload))->toBe('1526919030474-55');
});

it('deletes keys from the stream', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xdel')
        ->once()
        ->with('the-token', ['1-0', '2-0'])
        ->andReturn(2);

    expect((new Stream($connection))->delete('the-token', '1-0', ['2-0']))->toBe(2);
});

describe('StreamPayload', function () {
    it('casts to an array', function () {
        $payload = new StreamPayload('the-token', 'send.created', 'serialized');

        expect($payload->toArray())->toBe([
            'name' => 'send.created',
            'payload' => 'serialized',
        ])->and($payload->id)->toBe('*');
    });

    it('casts to json and serializes', function () {
        $payload = new StreamPayload('the-token', 'send.created', 'serialized');

        expect($payload->toJson())->toBe(json_encode($payload->toArray()))
            ->and($payload->jsonSerialize())->toBe($payload->toJson(1));
    });
});
