<?php

use Actengage\CaseyJones\Data\InstanceData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\assertCount;

it('gets paginated instances', function() {
    Client::mock([
        new Response(200, [], json_encode(new LengthAwarePaginator([
            InstanceData::mock(),
            InstanceData::mock()
        ], 1,  15)))
    ]);

    $response = Client::instances()->index();

    expect($response)->toBeInstanceOf(LengthAwarePaginator::class);
    
    assertCount(2, $response->items());

    expect($response->items()[0])->toBeInstanceOf(InstanceData::class);
});

it('gets all instances', function() {
    Client::mock([
        new Response(200, [], json_encode([
            InstanceData::mock(),
            InstanceData::mock(),
        ]))
    ]);

    $response = Client::instances()->all();

    expect($response)->toBeInstanceOf(Collection::class);
    
    assertCount(2, $response);

    expect($response->get(0))->toBeInstanceOf(InstanceData::class);
});

it('creates an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(InstanceData::mock()))
    ]);

    $response = Client::instances()->create([

    ]);

    expect($response)->toBeInstanceOf(InstanceData::class);
});

it('gets an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(InstanceData::mock()))
    ]);

    $response = Client::instances()->show(1);

    expect($response)->toBeInstanceOf(InstanceData::class);
});

it('updates an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(InstanceData::mock()))
    ]);

    $response = Client::instances()->update(1, [
        
    ]);

    expect($response)->toBeInstanceOf(InstanceData::class);
});

it('deletes an instance', function() {
    Client::mock([
        new Response(200, [], json_encode(InstanceData::mock()))
    ]);

    $response = Client::instances()->delete(1);

    expect($response)->toBeInstanceOf(InstanceData::class);
});