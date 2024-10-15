<?php

namespace Actengage\CaseyJones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Actengage\CaseyJones\Client
 * @method static array app()
 * @method static \Actengage\CaseyJones\Resources\InstanceResource instances()
 * @method static \Actengage\CaseyJones\Resources\SendResource sends()
 * @method static void personalAccessToken(string $key)
 * @method static void mock(\GuzzleHttp\Handler\MockHandler|array $handler)
 */
class Client extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'casey.client';
    }
}