<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists deployments', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Deployments/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->deployments()->list('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('deployments');
    expect($response->json('data.0.attributes.status'))->toBe('deployment.succeeded');
    expect($response->json('data.0.attributes.commit_hash'))->toBe('abc123def');
});

it('triggers deployment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Deployments/trigger.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->deployments()->trigger('env-01k7env000000000000000001');

    expect($response->status())->toBe(201);
    expect($response->json('data.type'))->toBe('deployments');
    expect($response->json('data.attributes.status'))->toBe('pending');
});

it('gets deployment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Deployments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->deployments()->get('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBeString();
    expect($response->json('data.attributes.status'))->toBe('deployment.succeeded');
    expect($response->json('data.relationships.environment.data.type'))->toBe('environments');
});

it('gets deployment logs', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Deployments/logs.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->deployments()->logs('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.logs'))->toContain('Building application');
});

it('cancels deployment', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->deployments()->cancel('env-01k7env000000000000000001');

    expect($response->status())->toBe(204);
});
