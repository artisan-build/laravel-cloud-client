<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentVariablesInsertMethod;
use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\CreateEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\DeleteEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\UpdateEnvironment;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

it('lists environments', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/list.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->list('app-01k7app000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.type'))->toBe('environments');
    expect($response->json('data.0.attributes.name'))->toBe('production');
});

it('creates environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 201,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->create(
        applicationId: 'app-01k7app000000000000000001',
        name: 'production',
        branch: 'main',
    );

    expect($response->status())->toBe(201);
});

it('gets environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->get('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('environments');
    expect($response->json('data.attributes.name'))->toBe('production');
    // The include GetEnvironment always sends is what makes these present at
    // all: a bare read comes back with no relationships object whatsoever.
    expect($response->json('data.relationships.database'))->toBe(['data' => null]);
    expect($response->json('data.relationships.cache'))->toBe(['data' => null]);
    expect($response->json('data.relationships.buckets'))->toBe(['data' => []]);
});

it('updates environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update('env-01k7env000000000000000001', name: 'staging');

    expect($response->status())->toBe(200);
});

it('sends resource attachment ids when updating environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        databaseSchemaId: 'dbschema-01k7schema0000000000001',
        cacheId: 'cache-01k7cache0000000000001',
        filesystemKeys: [
            ['id' => 'key-01k7assets00000000000001', 'disk' => 'assets', 'is_default_disk' => true],
            ['id' => 'key-01k7uploads0000000000001', 'disk' => 'uploads', 'is_default_disk' => false],
        ],
    );

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        $body = $response->getPendingRequest()->body()?->all();

        expect($request->getMethod())->toBe(Method::PATCH);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001');
        expect($body)->toBe([
            'database_schema_id' => 'dbschema-01k7schema0000000000001',
            'cache_id' => 'cache-01k7cache0000000000001',
            'filesystem_keys' => [
                ['id' => 'key-01k7assets00000000000001', 'disk' => 'assets', 'is_default_disk' => true],
                ['id' => 'key-01k7uploads0000000000001', 'disk' => 'uploads', 'is_default_disk' => false],
            ],
        ]);

        return true;
    });
});

it('omits unset resource attachment ids when updating environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        cacheId: 'cache-01k7cache0000000000001',
    );

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        $body = $response->getPendingRequest()->body()?->all();

        expect($body)->toBe([
            'cache_id' => 'cache-01k7cache0000000000001',
        ]);
        expect($body)->not->toHaveKeys(['database_schema_id', 'filesystem_keys']);

        return true;
    });
});

it('sends null attachment ids when detaching resources', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        databaseSchemaId: null,
        cacheId: '',
    );

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        $body = $response->getPendingRequest()->body()?->all();

        expect($body)->toBe([
            'database_schema_id' => null,
            'cache_id' => null,
        ]);

        return true;
    });
});

it('sends empty filesystem keys when updating environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        filesystemKeys: [],
    );

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        $body = $response->getPendingRequest()->body()?->all();

        expect($body)->toBe([
            'filesystem_keys' => [],
        ]);

        return true;
    });
});

it('deletes environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(status: 204),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->delete('env-01k7env000000000000000001');

    expect($response->status())->toBe(204);
});

it('refreshes environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->refresh('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
});

it('lists environment variables', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/variables.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->listVariables('env-01k7env000000000000000001');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0.key'))->toBe('APP_NAME');
});

it('creates environment variable', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->createVariable('env-01k7env000000000000000001', 'API_KEY', 'secret');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('environments');

    $mockClient->assertSent(function (CreateEnvironmentVariable $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::POST);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001/variables');
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'method' => 'set',
            'variables' => [
                ['key' => 'API_KEY', 'value' => 'secret'],
            ],
        ]);

        return true;
    });
});

it('adds environment variables with append method', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->addVariables(
        environmentId: 'env-01k7env000000000000000001',
        variables: [
            ['key' => 'API_KEY', 'value' => 'secret'],
            ['key' => 'APP_ENV', 'value' => 'production'],
        ],
        method: EnvironmentVariablesInsertMethod::Append,
    );

    expect($response->status())->toBe(200);
    expect($response->json('data.attributes.environment_variables.0.key'))->toBe('APP_NAME');

    $mockClient->assertSent(function (CreateEnvironmentVariable $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::POST);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001/variables');
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'method' => 'append',
            'variables' => [
                ['key' => 'API_KEY', 'value' => 'secret'],
                ['key' => 'APP_ENV', 'value' => 'production'],
            ],
        ]);

        return true;
    });
});

it('updates environment variable', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: json_encode(['data' => ['key' => 'API_KEY', 'value' => 'new-secret']]),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->updateVariable('env-01k7env000000000000000001', 'API_KEY', 'new-secret');

    expect($response->status())->toBe(200);
});

it('deletes environment variable', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->deleteVariable('env-01k7env000000000000000001', 'API_KEY');

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('environments');

    $mockClient->assertSent(function (DeleteEnvironmentVariable $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::POST);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001/variables/delete');
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'keys' => ['API_KEY'],
        ]);

        return true;
    });
});

it('sets the build command without touching anything else on the environment', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        buildCommand: 'composer install --no-dev && php artisan matte:provision-binary',
    );

    expect($response->status())->toBe(200);

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::PATCH);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001');
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'build_command' => 'composer install --no-dev && php artisan matte:provision-binary',
        ]);

        return true;
    });
});

it('omits the build command entirely when it is not passed', function () {
    // Omitted and null are DIFFERENT instructions: null clears an override and
    // restores Cloud's own default build, so a caller that only wanted to set
    // the PHP version must not send the field at all.
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        phpVersion: PhpVersion::Php84,
    );

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        expect($response->getPendingRequest()->body()?->all())->not->toHaveKey('build_command');

        return true;
    });
});

it('sends a null build command when clearing the override', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $client->environments()->update(
        environmentId: 'env-01k7env000000000000000001',
        buildCommand: null,
    );

    $mockClient->assertSent(function (UpdateEnvironment $request, Response $response): bool {
        expect($response->getPendingRequest()->body()?->all())->toBe(['build_command' => null]);

        return true;
    });
});

it('deletes environment variables in bulk', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Environments/show.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->environments()->deleteVariables('env-01k7env000000000000000001', ['API_KEY', 'APP_ENV']);

    expect($response->status())->toBe(200);
    expect($response->json('data.type'))->toBe('environments');

    $mockClient->assertSent(function (DeleteEnvironmentVariable $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::POST);
        expect($request->resolveEndpoint())->toBe('/environments/env-01k7env000000000000000001/variables/delete');
        expect($response->getPendingRequest()->body()?->all())->toBe([
            'keys' => ['API_KEY', 'APP_ENV'],
        ]);

        return true;
    });
});
