<?php

use Actengage\CaseyJones\Console\ListenStream;

it('throws when a stream payload is not a streamable event', function () {
    $command = app(ListenStream::class);

    (new ReflectionMethod($command, 'unserializePayload'))
        ->invoke($command, serialize('not-an-event'), '1-0');
})->throws(InvalidArgumentException::class, 'The Redis stream payload is not a streamable event.');
