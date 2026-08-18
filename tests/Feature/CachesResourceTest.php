<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists caches', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Caches/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->list();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('caches');
    expect($response->json('data.0.attributes.name'))->toBe('main-cache');
    expect($response->json('data.0.attributes.size'))->toBe('valkey-flex-250mb');
});

it('creates cache', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Caches/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->create('laravel_valkey', 'new-cache', 'us-east-1', CacheSize::ValkeyFlex250Mb, true, false);

    expect($response->status())->toBe(201);
});

it('gets cache', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Caches/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->get('cache-01k7cache0000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('caches');
    expect($response->json('data.attributes.name'))->toBe('main-cache');
    expect($response->json('data.relationships.environments.data.0.type'))->toBe('environments');
});

it('updates cache', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Caches/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->update('cache-01k7cache0000000000001', size: CacheSize::ValkeyFlex1Gb);

    expect($response->status())->toBe(200);
});

it('deletes cache', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->delete('cache-01k7cache0000000000001');

    expect($response->status())->toBe(204);
});

it('flushes cache', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: json_encode(['data' => ['message' => 'Cache flushed']]),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->caches()->flush('cache-01k7cache0000000000001');

    expect($response->status())->toBe(200);
});
