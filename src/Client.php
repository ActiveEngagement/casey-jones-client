<?php

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Resources\SendResource;
use Actengage\CaseyJones\Concerns\InteractsWithGuzzleClient;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Resources\InstanceResource;
use GuzzleHttp\Client as HttpClient;

/**
 * The official Casey Jones REST client.
 */
class Client
{
    use InteractsWithGuzzleClient, InteractsWithResponses;

    /**
     * Construct a new client.
     *
     * @param string|null $key
     */
    public function __construct(
        ?string $key = null
    ) {
        static::personalAccessToken($key ?? config('casey.api_key'));
    }

    /**
     * Get the authenticated app resource.
     *
     * @return array
     */
    public function app(): array
    {
        return $this->decode(static::$client->get('app'));
    }

    /**
     * Get all the available instances.
     *
     * @return InstanceResource
     */
    public function instances(): InstanceResource
    {
        return new InstanceResource($this);
    }

    /**
     * Search for sends API resource.
     *
     * @return SendResource
     */
    public function sends(): SendResource
    {
        return new SendResource($this);
    }

    /**
     * Create a new HttpClient with the given access token.
     *
     * @param ?string $key
     * @return HttpClient
     */
    public static function personalAccessToken(?string $key): void
    {
        static::client(new HttpClient([
            'base_uri' => config('casey.base_uri'),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $key ? sprintf('Bearer %s', $key): null
            ]
        ]));
    }
}