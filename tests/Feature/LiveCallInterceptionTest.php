<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\ListRegions;
use ArtisanBuild\LaravelCloudClient\Tests\Support\LiveCallAttempted;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

/*
|--------------------------------------------------------------------------
| No test in this package may reach live Laravel Cloud
|--------------------------------------------------------------------------
|
| The base TestCase configures a real token and the real production base URL,
| so a test that forgets a MockClient would otherwise send a genuine request to
| cloud.laravel.com. Nothing was doing that — this is about making the
| guarantee structural rather than a matter of everyone remembering.
|
| These tests deliberately make the call, because an assertion that "no live
| call happened" passes identically whether the guard works or whether no test
| ever calls out.
|
*/

it('CONTROL CASE: intercepts a deliberate live request', function (): void {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    // No mock client on the connector: without the guard this is a real HTTPS
    // request to https://cloud.laravel.com/api/meta/regions.
    expect(fn () => $client->send(new ListRegions))->toThrow(LiveCallAttempted::class);
});

it('CONTROL CASE: lets the same request through once the guard is removed', function (): void {
    // Proves the failure above comes from OUR guard rather than from Saloon
    // happening to refuse. With the guard gone and a real mock installed, the
    // request succeeds.
    MockClient::destroyGlobal();

    $client = (new LaravelCloudClient(apiToken: 'test-token'))->withMockClient(new MockClient([
        ListRegions::class => MockResponse::make(['data' => []], 200),
    ]));

    expect($client->send(new ListRegions)->status())->toBe(200);
});

it('reports the path and never the credential', function (): void {
    $client = new LaravelCloudClient(apiToken: 'lc_live_a_secret_credential_9f3a');

    try {
        $client->send(new ListRegions);
        throw new RuntimeException('The live call was not intercepted.');
    } catch (LiveCallAttempted $attempted) {
        expect($attempted->getMessage())
            ->toContain('/api/meta/regions')
            ->not->toContain('lc_live_a_secret_credential_9f3a');
    }
});

it('still lets a test install its own mock client', function (): void {
    // The guard must not get in the way of the package's own suite, which is
    // built entirely on per-connector mocks.
    $client = (new LaravelCloudClient(apiToken: 'test-token'))->withMockClient(new MockClient([
        ListRegions::class => MockResponse::make(['data' => [['id' => 'us-east-1']]], 200),
    ]));

    expect($client->send(new ListRegions)->json('data.0.id'))->toBe('us-east-1');
});
