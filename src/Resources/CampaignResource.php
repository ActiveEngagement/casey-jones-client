<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class CampaignResource
{
    use InteractsWithResponses;

    public function __construct(
        protected readonly Client $client
    ) {
        //
    }

    /**
     * Show the specified resource.
     *
     * @return array<array-key, mixed>
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function show(int $id): array
    {
        $response = $this->client->get(sprintf('campaigns/%s', $id));

        return $this->decode($response);
    }
}
