<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists background processes', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('BackgroundProcesses/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->list('ins-01k7ins000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('background_processes');
    expect($response->json('data.0.attributes.command'))->toBe('php artisan queue:work');
    expect($response->json('data.0.attributes.strategy_type'))->toBe('none');
});

it('creates background process', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('BackgroundProcesses/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->create('ins-01k7ins000000000000000001', 'php artisan queue:work', 2);

    expect($response->status())->toBe(201);
});

it('gets background process', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('BackgroundProcesses/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->get('bgp-01k7bgp000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBeString();
    expect($response->json('data.type'))->toBe('background_processes');
    expect($response->json('data.relationships.instance.data.type'))->toBe('instances');
});

it('updates background process', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('BackgroundProcesses/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->update('bgp-01k7bgp000000000000000001', processes: 4);

    expect($response->status())->toBe(200);
});

it('deletes background process', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->delete('bgp-01k7bgp000000000000000001');

    expect($response->status())->toBe(204);
});

it('restarts background process', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('BackgroundProcesses/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->backgroundProcesses()->restart('bgp-01k7bgp000000000000000001');

    expect($response->status())->toBe(200);
});
