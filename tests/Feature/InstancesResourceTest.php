<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists instances', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Instances/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->list('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('instances');
    // Cloud's own name for an environment's application instance.
    expect($response->json('data.0.attributes.type'))->toBe('app');
    expect($response->json('data.0.relationships.environment.data.id'))->toBe('env-01k7env000000000000000001');
});

it('creates instance', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Instances/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->create(
        environmentId: 'env-01k7env000000000000000001',
        name: 'web',
        type: InstanceType::Service,
        size: InstanceSize::Flex512Mb,
        scalingType: InstanceScalingType::Custom,
        minReplicas: 1,
        visibilityTimeout: null,
        shutdownTimeout: null,
        maxReplicas: 2,
    );

    expect($response->status())->toBe(201);
});

it('gets instance', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Instances/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->get('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBeString();
    expect($response->json('data.type'))->toBe('instances');
    expect($response->json('data.attributes.size'))->toBe('flex-512mb');
});

it('updates instance', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Instances/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->update('env-01k7env000000000000000001', size: InstanceSize::Flex512Mb);

    expect($response->status())->toBe(200);
});

it('deletes instance', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->delete('env-01k7env000000000000000001');

    expect($response->status())->toBe(204);
});

it('restarts instance', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Instances/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->instances()->restart('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
});
