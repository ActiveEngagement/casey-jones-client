<?php

namespace Actengage\CaseyJones\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

trait InteractsWithGuzzleClient
{
    /**
     * The http client instance.
     *
     * @var Client
     */
    protected static Client $client;

    /**
     * @inheritDoc
     */
    public function get($uri, array $options = []): ResponseInterface
    {
        return static::$client->get($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function head($uri, array $options = []): ResponseInterface
    {
        return static::$client->head($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function post($uri, array $options = []): ResponseInterface
    {
        return static::$client->post($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function put($uri, array $options = []): ResponseInterface
    {
        return static::$client->put($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function patch($uri, array $options = []): ResponseInterface
    {
        return static::$client->patch($uri, $options);
    }
    
    /**
     * @inheritDoc
     */
    public function delete($uri, array $options = []): ResponseInterface
    {
        return static::$client->delete($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function getAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->getAsync($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function headAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->headAsync($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function postAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->postAsync($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function putAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->putAsync($uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function patchAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->patchAsync($uri, $options);
    }
    
    /**
     * @inheritDoc
     */
    public function deleteAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->deleteAsync($uri, $options);
    }

    /**
     * Mock an HTTP client.
     *
     * @param HttpClient $client
     * @return void
     */
    public static function client(Client $client): void
    {
        static::$client = $client;
    }

    /**
     * Mock an HTTP client.
     *
     * @param MockHandler|array $handler
     * @return void
     */
    public static function mock(MockHandler|array $handler): void
    {
        static::$client = new Client([
            'handler' => is_array($handler) ? new MockHandler($handler) : $handler
        ]);
    }
}