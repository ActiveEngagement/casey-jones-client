<?php

use Actengage\CaseyJones\Data\SendData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\assertCount;

it('gets paginated sends', function() {
    Client::mock([
        new Response(200, [], json_encode(new LengthAwarePaginator([
            SendData::mock(),
            SendData::mock()
        ], 1,  15)))
    ]);

    $response = Client::sends()->index();

    expect($response)->toBeInstanceOf(LengthAwarePaginator::class);
    
    assertCount(2, $response->items());

    expect($response->items()[0])->toBeInstanceOf(SendData::class);
});

it('creates a send', function() {
    Client::mock([
        new Response(200, [], json_encode(SendData::mock()))
    ]);

    $response = Client::sends()->create([

    ]);

    expect($response)->toBeInstanceOf(SendData::class);
});

it('gets a send', function() {
    Client::mock([
        new Response(200, [], json_encode(SendData::mock()))
    ]);

    $response = Client::sends()->show(str()->uuid());

    expect($response)->toBeInstanceOf(SendData::class);
});

it('updates an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(SendData::mock()))
    ]);

    $response = Client::sends()->update(1, [
        
    ]);

    expect($response)->toBeInstanceOf(SendData::class);
});

it('deletes an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(SendData::mock()))
    ]);

    $response = Client::sends()->delete(1);

    expect($response)->toBeInstanceOf(SendData::class);
});