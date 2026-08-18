<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists database clusters', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->list();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('databases');
    expect($response->json('data.0.attributes.type'))->toBe('laravel_mysql_84');
    expect($response->json('data.0.attributes.config.size'))->toBe('mysql-flex-512mb');
});

it('creates database cluster', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->create(DatabaseType::LaravelMySql84, 'main-db', 'us-east-1', [
        'size' => 'mysql-flex-512mb',
        'storage' => 5,
        'is_public' => false,
        'uses_scheduled_snapshots' => true,
        'retention_days' => 7,
    ]);

    expect($response->status())->toBe(201);
});

it('gets database cluster', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->get('db-01k7db000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('databases');
    expect($response->json('data.attributes.name'))->toBe('main-db');
    expect($response->json('data.relationships.databases.data.0.type'))->toBe('databaseSchemas');
});

it('updates database cluster', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->update('db-01k7db000000000000000001', [
        'size' => 'mysql-flex-1gb',
        'storage' => 10,
        'is_public' => false,
        'uses_scheduled_snapshots' => true,
        'retention_days' => 7,
    ]);

    expect($response->status())->toBe(200);
});

it('deletes database cluster', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->delete('db-01k7db000000000000000001');

    expect($response->status())->toBe(204);
});

it('lists databases', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/databases.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->listDatabases('db-01k7db000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.0.type'))->toBe('databaseSchemas');
    expect($response->json('data.0.attributes.name'))->toBe('laravel');
    expect($response->json('data.0.relationships.database.data.id'))->toBe('db-01k7db000000000000000001');
});

it('creates database', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: json_encode(['data' => ['name' => 'new-db']]),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->createDatabase('db-01k7db000000000000000001', 'new-db');

    expect($response->status())->toBe(201);
});

it('deletes database', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->deleteDatabase('db-01k7db000000000000000001', 'old-db');

    expect($response->status())->toBe(204);
});

it('lists database users', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('DatabaseClusters/users.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->listUsers('db-01k7db000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.0.username'))->toBe('laravel');
});

it('creates database user', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: json_encode(['data' => ['username' => 'new-user']]),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->createUser('db-01k7db000000000000000001', 'new-user');

    expect($response->status())->toBe(201);
});

it('deletes database user', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->databaseClusters()->deleteUser('db-01k7db000000000000000001', 'old-user');

    expect($response->status())->toBe(204);
});
