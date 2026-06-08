<?php

use Actengage\CaseyJones\Facades\Client;
use Actengage\CaseyJones\Resources\CampaignResource;
use GuzzleHttp\Psr7\Response;

it('exposes a campaigns resource from the client', function () {
    expect(Client::campaigns())->toBeInstanceOf(CampaignResource::class);
});

it('shows a campaign', function () {
    Client::mock([
        new Response(200, [], json_encode(['id' => 1, 'name' => 'Test Campaign'])),
    ]);

    expect(Client::campaigns()->show(1))->toBe(['id' => 1, 'name' => 'Test Campaign']);
});
