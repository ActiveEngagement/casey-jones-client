<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsAudienceData;

class SendAudienceResource
{
    use InteractsWithResponses;

    public function __construct(
        protected readonly Client $client,
        protected readonly string $send_id
    ) {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show(): MessageGearsAudienceData
    {
        $response = $this->client->get(sprintf(
            'sends/%s/audience', $this->send_id
        ));

        return MessageGearsAudienceData::from($this->decode($response));
    }
}
