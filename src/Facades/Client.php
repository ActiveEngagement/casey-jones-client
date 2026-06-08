<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Actengage\CaseyJones\Client
 *
 * @method static array<array-key, mixed> app()
 * @method static \Actengage\CaseyJones\Resources\InstanceResource instances()
 * @method static \Actengage\CaseyJones\Resources\SendResource sends()
 * @method static void personalAccessToken(?string $key)
 * @method static void mock(\GuzzleHttp\Handler\MockHandler|array<int, mixed> $handler)
 */
class Client extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'casey.client';
    }
}
