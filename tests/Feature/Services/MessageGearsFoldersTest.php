<?php

use Actengage\CaseyJones\Data\MessageGearsAudienceData;
use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Actengage\CaseyJones\Facades\MessageGears;
use Actengage\CaseyJones\Services\MessageGears as MessageGearsService;
use Actengage\MessageGears\Cloud;
use Actengage\MessageGears\Facades\Accelerator;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

it('sets the cloud instance fluently', function () {
    expect(MessageGears::cloud(Mockery::mock(Cloud::class)))
        ->toBeInstanceOf(MessageGearsService::class);
});

it('returns an empty page when the response content is malformed', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], json_encode(['content' => 'not-an-array', 'totalElements' => 0, 'size' => 50, 'last' => true])),
        ])
    );

    expect($service->getTemplates()->items())->toBe([]);
});

it('gets a paginated list of templates', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], json_encode([
                'content' => [MessageGearsTemplateData::mock()->toArray()],
                'totalElements' => 1,
                'size' => 50,
            ])),
        ])
    );

    $templates = $service->getTemplates();

    expect($templates)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($templates->items()[0])->toBeInstanceOf(MessageGearsTemplateData::class);
});

it('gets an audience', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], MessageGearsAudienceData::mock()->toJson()),
        ])
    );

    expect($service->getAudience(1))->toBeInstanceOf(MessageGearsAudienceData::class);
});

it('gets a paginated list of folders', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], json_encode([
                'content' => [MessageGearsFolderData::mock()->toArray()],
                'totalElements' => 1,
                'size' => 50,
            ])),
        ])
    );

    $folders = $service->getFolders();

    expect($folders)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($folders->items()[0])->toBeInstanceOf(MessageGearsFolderData::class);
});

it('gets all folders across pages', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], json_encode([
                'content' => [
                    MessageGearsFolderData::mock(['id' => 1])->toArray(),
                    MessageGearsFolderData::mock(['id' => 2])->toArray(),
                ],
                'last' => true,
            ])),
        ])
    );

    $folders = $service->getAllFolders();

    expect($folders)->toBeInstanceOf(Collection::class)
        ->and($folders)->toHaveCount(2);
});

it('builds a folder tree from the flat folder list', function () {
    $service = MessageGears::accelerator(
        Accelerator::mock([
            new Response(200, [], json_encode([
                'content' => [
                    MessageGearsFolderData::mock(['id' => 1, 'name' => 'Root', 'parentId' => null])->toArray(),
                    MessageGearsFolderData::mock(['id' => 2, 'name' => 'Child', 'parentId' => 1])->toArray(),
                ],
                'last' => true,
            ])),
        ])
    );

    $tree = $service->getFolderTree();

    expect($tree)->toBeArray()
        ->and($tree)->toHaveCount(1)
        ->and($tree[0]->id)->toBe(2);
});
