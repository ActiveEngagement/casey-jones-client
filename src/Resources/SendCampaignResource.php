<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;

class SendCampaignResource
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
    public function show(): MessageGearsMarketingCampaignData
    {
        $response = $this->client->get(sprintf(
            'sends/%s/campaign', $this->send_id
        ));

        return MessageGearsMarketingCampaignData::from($this->decode($response));
    }
}
