<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;

class SendCampaignResource
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
     * @return \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData
     */
    public function show(): MessageGearsMarketingCampaignData
    {
        $response = $this->client->get(sprintf(
            'sends/%s/campaign', $this->send_id
        ));

        return MessageGearsMarketingCampaignData::from($this->decode($response));
    }

}