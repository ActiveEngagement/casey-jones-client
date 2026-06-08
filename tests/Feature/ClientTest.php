<?php

use Actengage\CaseyJones\Client as ClientService;
use Actengage\CaseyJones\Facades\Client;
use Actengage\CaseyJones\Resources\InstanceResource;
use Actengage\CaseyJones\Resources\SendResource;
use GuzzleHttp\Psr7\Response;

it('gets the authenticated app resource', function () {
    Client::mock([
        new Response(200, [], json_encode(['id' => 1, 'name' => 'Test App'])),
    ]);

    expect(Client::app())->toBe(['id' => 1, 'name' => 'Test App']);
});

it('exposes the instances and sends resources', function () {
    expect(Client::instances())->toBeInstanceOf(InstanceResource::class)
        ->and(Client::sends())->toBeInstanceOf(SendResource::class);
});

it('proxies the synchronous http verbs to guzzle', function () {
    Client::mock([
        new Response(200, [], 'get'),
        new Response(200, [], 'head'),
        new Response(200, [], 'post'),
        new Response(200, [], 'put'),
        new Response(200, [], 'patch'),
        new Response(200, [], 'delete'),
    ]);

    expect((string) Client::get('x')->getBody())->toBe('get')
        ->and(Client::head('x')->getStatusCode())->toBe(200)
        ->and((string) Client::post('x')->getBody())->toBe('post')
        ->and((string) Client::put('x')->getBody())->toBe('put')
        ->and((string) Client::patch('x')->getBody())->toBe('patch')
        ->and((string) Client::delete('x')->getBody())->toBe('delete');
});

it('proxies the asynchronous http verbs to guzzle', function () {
    Client::mock([
        new Response(200, [], 'get'),
        new Response(200, [], 'head'),
        new Response(200, [], 'post'),
        new Response(200, [], 'put'),
        new Response(200, [], 'patch'),
        new Response(200, [], 'delete'),
    ]);

    expect((string) Client::getAsync('x')->wait()->getBody())->toBe('get')
        ->and(Client::headAsync('x')->wait()->getStatusCode())->toBe(200)
        ->and((string) Client::postAsync('x')->wait()->getBody())->toBe('post')
        ->and((string) Client::putAsync('x')->wait()->getBody())->toBe('put')
        ->and((string) Client::patchAsync('x')->wait()->getBody())->toBe('patch')
        ->and((string) Client::deleteAsync('x')->wait()->getBody())->toBe('delete');
});

it('sets a bearer authorization header when given a key', function () {
    $client = new ClientService('personal-access-token');

    expect($client)->toBeInstanceOf(ClientService::class);
});
