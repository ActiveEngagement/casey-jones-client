<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;

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
     * @param int $id
     * @return array
     * @throws \GuzzleHttp\Exception\ServerException
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function show(int $id): array
    {
        $response = $this->client->get(sprintf('campaigns/%s', $id));

        return $this->decode($response);
    }
}