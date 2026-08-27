<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\CreateInstance;
use ArtisanBuild\LaravelCloudClient\Support\BackgroundProcess;
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

it('omits background_processes entirely when the instance declares none', function () {
    $request = new CreateInstance(
        environmentId: 'env-01k7env000000000000000001',
        name: 'web',
        type: InstanceType::Service,
        size: InstanceSize::Flex512Mb,
        scalingType: InstanceScalingType::Custom,
        minReplicas: 1,
        visibilityTimeout: null,
        shutdownTimeout: null,
    );

    // ABSENT, not `[]` and not `null`. The schema types this as an array whose
    // every item requires `type` and `processes`, so an empty array is a
    // statement Cloud gets to have an opinion about; saying nothing is not.
    expect($request->body()->all())->not->toHaveKey('background_processes');
});

it('serializes a worker background process without a command', function () {
    $request = new CreateInstance(
        environmentId: 'env-01k7env000000000000000001',
        name: 'queue',
        type: InstanceType::ManagedQueue,
        size: 'mq-1',
        scalingType: InstanceScalingType::Auto,
        minReplicas: null,
        visibilityTimeout: null,
        shutdownTimeout: null,
        backgroundProcesses: [BackgroundProcess::worker(processes: 2, queue: 'default,emails')],
    );

    expect($request->body()->all()['background_processes'])->toBe([[
        'type' => 'worker',
        'processes' => 2,
        'config' => ['connection' => 'cloud', 'queue' => 'default,emails'],
    ]]);
});

it('serializes a custom background process with its command and no config', function () {
    $request = new CreateInstance(
        environmentId: 'env-01k7env000000000000000001',
        name: 'web',
        type: InstanceType::Service,
        size: InstanceSize::Flex512Mb,
        scalingType: InstanceScalingType::Custom,
        minReplicas: 1,
        visibilityTimeout: null,
        shutdownTimeout: null,
        backgroundProcesses: [BackgroundProcess::custom('php artisan my:command')],
    );

    expect($request->body()->all()['background_processes'])->toBe([[
        'type' => 'custom',
        'processes' => 1,
        'command' => 'php artisan my:command',
    ]]);
});

it('refuses a process count outside the schema range', function (int $processes) {
    expect(fn () => BackgroundProcess::worker(processes: $processes))
        ->toThrow(InvalidArgumentException::class, 'between 1 and 10');
})->with([0, -1, 11]);

it('refuses an empty command on a custom background process', function () {
    expect(fn () => BackgroundProcess::custom('   '))
        ->toThrow(InvalidArgumentException::class, 'command must not be empty');
});

it('refuses an empty connection or queue on a worker', function () {
    expect(fn () => BackgroundProcess::worker(connection: ''))
        ->toThrow(InvalidArgumentException::class, 'connection must not be empty');

    expect(fn () => BackgroundProcess::worker(queue: ' '))
        ->toThrow(InvalidArgumentException::class, 'queue must not be empty');
});
