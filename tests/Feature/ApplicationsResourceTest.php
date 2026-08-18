<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\UpdateApplication;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

it('lists applications', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Applications/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->list();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('applications');
    expect($response->json('data.0.attributes.name'))->toBe('My Application');
});

it('creates application', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Applications/create.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->create(
        name: 'New Application',
        repository: 'https://github.com/user/new-repo',
        region: 'us-east-1',
    );

    expect($response->status())->toBe(201);
    expect($response->json('data.type'))->toBe('applications');
    expect($response->json('data.attributes.name'))->toBe('New Application');
});

it('gets application by id', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Applications/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->get('app-01k7app000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBeString();
    expect($response->json('data.type'))->toBe('applications');
    expect($response->json('data.attributes.name'))->toBe('My Application');
});

it('updates application', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Applications/update.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->update('app-01k7app000000000000000001', name: 'Updated Application');

    expect($response->status())->toBe(200);
    expect($response->json('data.attributes.name'))->toBe('Updated Application');
});

it('sends null slack channel when clearing application slack integration', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Applications/update.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->update('app-01k7app000000000000000001', slackChannel: null);

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateApplication $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::PATCH);
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'slack_channel' => null,
        ]);

        return true;
    });
});

it('deletes application', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->applications()->delete('app-01k7app000000000000000001');

    expect($response->status())->toBe(204);
});
