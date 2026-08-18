<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists buckets', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->listBuckets();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('filesystems');
    expect($response->json('data.0.attributes.name'))->toBe('app-uploads');
    expect($response->json('data.0.attributes.visibility'))->toBe('private');
});

it('creates bucket', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->createBucket('new-bucket', 'private', 'us', 'primary-key', 'read_write');

    expect($response->status())->toBe(201);
});

it('gets bucket', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->getBucket('bucket-01k7bucket0000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('filesystems');
    expect($response->json('data.attributes.name'))->toBe('app-uploads');
    expect($response->json('data.relationships.keys.data.0.type'))->toBe('filesystemKeys');
});

it('updates bucket', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->updateBucket('bucket-01k7bucket0000000000001', public: true);

    expect($response->status())->toBe(200);
});

it('deletes bucket', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->deleteBucket('bucket-01k7bucket0000000000001');

    expect($response->status())->toBe(204);
});

it('lists bucket access keys', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/access-keys.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->listAccessKeys('bucket-01k7bucket0000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.0.type'))->toBe('filesystemKeys');
    expect($response->json('data.0.attributes.name'))->toBe('primary-key');
    expect($response->json('data.0.relationships.filesystem.data.type'))->toBe('filesystems');
});

it('creates bucket access key', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('ObjectStorage/create-access-key.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->createAccessKey('bucket-01k7bucket0000000000001', 'new-key', 'read_write');

    expect($response->status())->toBe(201);
    expect($response->json('data.type'))->toBe('filesystemKeys');
    expect($response->json('data.attributes.access_key_secret'))->not()->toBeNull();
});

it('deletes bucket access key', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->objectStorage()->deleteAccessKey('bucket-01k7bucket0000000000001', 'key-01k7key000000000000000001');

    expect($response->status())->toBe(204);
});
