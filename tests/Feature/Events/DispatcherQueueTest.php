<?php

use Actengage\CaseyJones\Events\Dispatcher;

it('throws when the queue resolver has not been set', function () {
    $dispatcher = new Dispatcher;

    (new ReflectionMethod($dispatcher, 'resolveQueue'))->invoke($dispatcher);
})->throws(RuntimeException::class, 'The queue resolver has not been set.');
