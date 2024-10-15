<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsAudienceData;

class SendAudienceResource
{
    use InteractsWithResponses;
    
    public function __construct(
        protected readonly Client $client,
        protected readonly int $send_id
    ) {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param string $id
     * @return \Actengage\CaseyJones\Data\MessageGearsAudienceData
     */
    public function show(): MessageGearsAudienceData
    {
        $response = $this->client->get(sprintf(
            'sends/%s/audience', $this->send_id
        ));

        return MessageGearsAudienceData::from($this->decode($response));
    }
}