<?php

use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNull;

it('gets paginated templates', function() {
    Client::mock([
        new Response(200, [], json_encode(new LengthAwarePaginator([
            MessageGearsTemplateData::mock(),
            MessageGearsTemplateData::mock()
        ], 1,  15)))
    ]);

    $response = Client::instances()->templates(1)->index();


    expect($response)->toBeInstanceOf(LengthAwarePaginator::class);
    
    assertCount(2, $response->items());

    expect($response->items()[0])->toBeInstanceOf(MessageGearsTemplateData::class);
});

it('creates a template', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsTemplateData::mock()))
    ]);

    $response = Client::instances()->templates(1)->create([

    ]);

    expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
});

it('gets an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsTemplateData::mock()))
    ]);

    $response = Client::instances()->templates(1)->show(1);

    expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
});

it('updates an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsTemplateData::mock()))
    ]);

    $response = Client::instances()->templates(1)->update(1, [
        
    ]);

    expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
});

it('deletes an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(MessageGearsTemplateData::mock()))
    ]);

    /** @var null */
    $response = Client::instances()->templates(1)->delete(1);

    assertNull($response);
});