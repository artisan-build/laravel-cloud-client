<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\DomainVerificationMethod;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists domains', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->list('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('domains');
    expect($response->json('data.0.attributes.name'))->toBe('example.com');
    expect($response->json('data.0.relationships.environment.data.id'))->toBe('env-01k7env000000000000000001');
});

it('adds domain', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->add('env-01k7env000000000000000001', 'example.com');

    expect($response->status())->toBe(201);
});

it('gets domain', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->get('dom-01k7dom000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('domains');
    expect($response->json('data.attributes.name'))->toBe('example.com');
    expect($response->json('data.attributes.hostname_status'))->toBe('verified');
});

it('updates domain', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->update('dom-01k7dom000000000000000001', DomainVerificationMethod::RealTime);

    expect($response->status())->toBe(200);
});

it('deletes domain', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->delete('dom-01k7dom000000000000000001');

    expect($response->status())->toBe(204);
});

it('requests ssl certificate', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/ssl-status.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->requestSsl('dom-01k7dom000000000000000001');

    expect($response->status())->toBe(200);
});

it('gets ssl certificate status', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Domains/ssl-status.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->domains()->getSslStatus('dom-01k7dom000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.status'))->toBe('active');
});
