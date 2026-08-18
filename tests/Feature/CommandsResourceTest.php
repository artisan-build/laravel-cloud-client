<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('lists commands', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Commands/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->commands()->list('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('commands');
    expect($response->json('data.0.attributes.command'))->toBe('php artisan migrate');
    expect($response->json('data.0.attributes.status'))->toBe('command.success');
});

it('executes command', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Commands/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->commands()->execute('env-01k7env000000000000000001', 'php artisan migrate');

    expect($response->status())->toBe(201);
});

it('gets command', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Commands/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->commands()->get('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBeString();
    expect($response->json('data.type'))->toBe('commands');
    expect($response->json('data.relationships.environment.data.type'))->toBe('environments');
});

it('gets command output', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Commands/output.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->commands()->output('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.output'))->toContain('Migration table created');
});

it('cancels command', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->commands()->cancel('env-01k7env000000000000000001');

    expect($response->status())->toBe(204);
});
