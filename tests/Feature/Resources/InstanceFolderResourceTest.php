<?php

use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Actengage\CaseyJones\Facades\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\assertCount;

it('gets paginated folders', function() {
    Client::mock([
        new Response(200, [], json_encode(new LengthAwarePaginator([
            MessageGearsFolderData::mock(),
            MessageGearsFolderData::mock()
        ], 1,  15)))
    ]);

    $response = Client::instances()->folders(1)->index();

    expect($response)->toBeInstanceOf(LengthAwarePaginator::class);
    
    assertCount(2, $response->items());

    expect($response->items()[0])->toBeInstanceOf(MessageGearsFolderData::class);
});

it('gets all folders', function() {
    Client::mock([
        new Response(200, [], json_encode([
            MessageGearsFolderData::mock(),
            MessageGearsFolderData::mock(),
        ]))
    ]);

    $response = Client::instances()->folders(1)->all();

    expect($response)->toBeInstanceOf(Collection::class);
    
    assertCount(2, $response);

    expect($response->get(0))->toBeInstanceOf(MessageGearsFolderData::class);
});

it('get a tree of folders', function() {
    Client::mock([
        new Response(200, [], json_encode([
            [
                'id' => -3,
                'name' => 'templates',
                'children' => [
                    [
                        'id' => 1,
                        'name' => 'Template 1',
                        'parentId' => -3,
                        'children' => []
                    ],
                    [
                        'id' => 2,
                        'name' => 'Template 2',
                        'parentId' => -3,
                        'children' => [
                            [
                                'id' => 3,
                                'name' => 'Template 3',
                                'parentId' => 2,
                                'children' => []
                            ]
                        ]
                    ]
                ]
            ],
        ]))
    ]);

    $response = Client::instances()->folders(1)->tree();

    expect($response)->toBeArray();
});