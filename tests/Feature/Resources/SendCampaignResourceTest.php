<?php

use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;

it('gets a send campaign', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsMarketingCampaignData::mock()))
    ]);

    $response = Client::sends()->campaign(1)->show();

    expect($response)->toBeInstanceOf(MessageGearsMarketingCampaignData::class);
});