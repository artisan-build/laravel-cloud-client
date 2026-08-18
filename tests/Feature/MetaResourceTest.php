<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\GetOrganization;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\ListRegions;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

it('gets organization metadata', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Meta/organization.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->meta()->organization();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.id'))->toBe('org_123');
    expect($response->json('data.type'))->toBe('organizations');
    expect($response->json('data.attributes.name'))->toBe('Artisan Build');
    expect($response->json('data.attributes.slug'))->toBe('artisan-build');

    $mockClient->assertSent(function (GetOrganization $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::GET);
        expect($request->resolveEndpoint())->toBe('/meta/organization');

        return true;
    });
});

it('lists regions', function () {
    $mockClient = new MockClient([
        MockResponse::make(
            body: $this->getFixture('Meta/regions.json'),
            status: 200,
        ),
    ]);

    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    $response = $client->meta()->regions();

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('data.0'))->toHaveKeys(['region', 'label', 'flag']);
    expect($response->json('data.0.region'))->toBe('us-east-1');

    $mockClient->assertSent(function (ListRegions $request, Response $response): bool {
        expect($request->getMethod())->toBe(Method::GET);
        expect($request->resolveEndpoint())->toBe('/meta/regions');

        return true;
    });
});

it('skips read-only live metadata verification without a laravel cloud token', function () {
    if (getenv('LARAVEL_CLOUD_TOKEN') !== false) {
        $this->markTestSkipped('Skip guard proof only runs when LARAVEL_CLOUD_TOKEN is unset.');
    }

    $this->markTestSkipped('Set LARAVEL_CLOUD_TOKEN to run read-only live Laravel Cloud metadata verification.');
});

it('verifies live metadata shapes read-only', function () {
    $token = getenv('LARAVEL_CLOUD_TOKEN');

    if (! is_string($token) || $token === '') {
        $this->markTestSkipped('Set LARAVEL_CLOUD_TOKEN to run read-only live Laravel Cloud metadata verification.');
    }

    $client = new LaravelCloudClient(apiToken: $token);

    $organization = $client->meta()->organization();

    expect($organization->status())->toBeGreaterThanOrEqual(200)->toBeLessThan(300);
    expect($organization->json('data.id'))->toBeString()->not->toBe('');
    expect($organization->json('data.type'))->toBe('organizations');
    expect($organization->json('data.attributes.name'))->toBeString()->not->toBe('');
    expect($organization->json('data.attributes.slug'))->toBeString()->not->toBe('');

    $regions = $client->meta()->regions();

    expect($regions->status())->toBeGreaterThanOrEqual(200)->toBeLessThan(300);
    expect($regions->json('data'))->toBeArray()->not->toBeEmpty();
    expect($regions->json('data.0.region'))->toBeString()->not->toBe('');
    expect($regions->json('data.0.label'))->toBeString()->not->toBe('');
    expect($regions->json('data.0.flag'))->toBeString()->not->toBe('');

    $environmentId = getenv('LARAVEL_CLOUD_ENVIRONMENT_ID');

    if (is_string($environmentId) && $environmentId !== '') {
        $environment = $client->environments()->get($environmentId);

        expect($environment->status())->toBeGreaterThanOrEqual(200)->toBeLessThan(300);
        expect($environment->json('data.id'))->toBeString()->not->toBe('');
        expect($environment->json('data.type'))->toBe('environments');
        expect($environment->json('data.relationships'))->toBeArray();
    }

    // Live attach verification is deferred to U7, where provisioning creates a disposable environment it owns.
});
