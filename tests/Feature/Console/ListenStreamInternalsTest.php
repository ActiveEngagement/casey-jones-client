<?php

use Actengage\CaseyJones\Console\ListenStream;
use Actengage\CaseyJones\Redis\Streamable;
use Illuminate\Redis\Connections\Connection;

class ExposedListenStream extends ListenStream
{
    /**
     * @return array<int|string, mixed>
     */
    public function exposeRead(Connection $connection, string $token): array
    {
        $lastId = '0-0';
        $result = [];

        $this->read($connection, $lastId, $token)->then(function (array $messages) use (&$result): void {
            $result = $messages;
        });

        return $result;
    }

    public function exposeUnserialize(string $payload, string $key): Streamable
    {
        return $this->unserializePayload($payload, $key);
    }
}

it('returns an empty array when the redis stream returns a non-array', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('xread')->andReturn(false);

    expect((new ExposedListenStream)->exposeRead($connection, 'the-token'))->toBe([]);
});

it('throws when a stream payload is not a streamable event', function () {
    (new ExposedListenStream)->exposeUnserialize(serialize('not-an-event'), '1-0');
})->throws(InvalidArgumentException::class);
