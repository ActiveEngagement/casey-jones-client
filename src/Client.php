<?php

declare(strict_types=1);

namespace Actengage\CaseyJones;

use Actengage\CaseyJones\Concerns\InteractsWithGuzzleClient;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Resources\CampaignResource;
use Actengage\CaseyJones\Resources\InstanceResource;
use Actengage\CaseyJones\Resources\SendResource;
use GuzzleHttp\Client as HttpClient;

/**
 * The official Casey Jones REST client.
 */
class Client
{
    use InteractsWithGuzzleClient, InteractsWithResponses;

    /**
     * Construct a new client.
     */
    public function __construct(
        ?string $key = null
    ) {
        $apiKey = $key ?? config('casey.api_key');

        static::personalAccessToken(is_string($apiKey) ? $apiKey : null);
    }

    /**
     * Get the authenticated app resource.
     *
     * @return array<array-key, mixed>
     */
    public function app(): array
    {
        return $this->decode(static::$client->get('app'));
    }

    /**
     * Get all the available instances.
     */
    public function instances(): InstanceResource
    {
        return new InstanceResource($this);
    }

    /**
     * Search for sends API resource.
     */
    public function sends(): SendResource
    {
        return new SendResource($this);
    }

    /**
     * Get the campaigns API resource.
     */
    public function campaigns(): CampaignResource
    {
        return new CampaignResource($this);
    }

    /**
     * Create a new HttpClient with the given access token.
     */
    public static function personalAccessToken(?string $key): void
    {
        static::client(new HttpClient([
            'base_uri' => config('casey.base_uri'),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $key ? sprintf('Bearer %s', $key) : null,
            ],
        ]));
    }
}
