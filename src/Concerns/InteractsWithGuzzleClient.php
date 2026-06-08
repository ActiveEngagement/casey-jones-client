<?php

namespace Actengage\CaseyJones\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

trait InteractsWithGuzzleClient
{
    /**
     * The http client instance.
     */
    protected static Client $client;

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function get($uri, array $options = []): ResponseInterface
    {
        return static::$client->get($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function head($uri, array $options = []): ResponseInterface
    {
        return static::$client->head($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function post($uri, array $options = []): ResponseInterface
    {
        return static::$client->post($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function put($uri, array $options = []): ResponseInterface
    {
        return static::$client->put($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function patch($uri, array $options = []): ResponseInterface
    {
        return static::$client->patch($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function delete($uri, array $options = []): ResponseInterface
    {
        return static::$client->delete($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function getAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->getAsync($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function headAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->headAsync($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function postAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->postAsync($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function putAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->putAsync($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function patchAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->patchAsync($uri, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @param  string|UriInterface  $uri
     * @param  array<string, mixed>  $options
     */
    public function deleteAsync($uri, array $options = []): PromiseInterface
    {
        return static::$client->deleteAsync($uri, $options);
    }

    /**
     * Set the HTTP client.
     */
    public static function client(Client $client): void
    {
        static::$client = $client;
    }

    /**
     * Mock an HTTP client.
     *
     * @param  MockHandler|array<int, mixed>  $handler
     */
    public static function mock(MockHandler|array $handler): void
    {
        static::$client = new Client([
            'handler' => is_array($handler) ? new MockHandler($handler) : $handler,
        ]);
    }
}
