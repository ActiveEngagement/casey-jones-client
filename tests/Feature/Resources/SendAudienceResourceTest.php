<?php

use Actengage\CaseyJones\Data\MessageGearsAudienceData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;

it('gets a send audience', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsAudienceData::mock()))
    ]);

    $response = Client::sends()->audience(1)->show();

    expect($response)->toBeInstanceOf(MessageGearsAudienceData::class);
});