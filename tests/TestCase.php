<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Tests;

use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Providers\LaravelCloudServiceProvider;
use ArtisanBuild\LaravelCloudClient\Tests\Support\LiveCallAttempted;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class TestCase extends Orchestra
{
    public const BODY_NOT_CHECKED = '__laravel_cloud_body_not_checked__';

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelCloudServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('laravel-cloud-client.api_token', 'test-token');
        $app['config']->set('laravel-cloud-client.base_url', 'https://cloud.laravel.com/api');
    }

    /**
     * Fail closed: no test in this package may reach Laravel Cloud.
     *
     * This base configures a token AND the real production base URL, so a test
     * that forgets to install a MockClient would previously have sent a genuine
     * HTTP request to cloud.laravel.com. Nothing here was doing that — but "no
     * test can reach live Cloud" has to be a property of the harness rather
     * than of everybody remembering, and the application's own suite has had
     * exactly this guard since it was written.
     *
     * Saloon consults a global mock client for any request that does not carry
     * its own, and connector-keyed entries match every request through that
     * connector — so this catches every possible call. A test that needs canned
     * responses installs its own MockClient, which Saloon prefers, and still
     * touches no network.
     *
     * LiveCallInterceptionTest deliberately makes a call and proves this fires.
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        MockClient::destroyGlobal();

        MockClient::global([
            LaravelCloudClient::class => static function (PendingRequest $request): never {
                throw LiveCallAttempted::to($request->getUrl());
            },
        ]);
    }

    #[\Override]
    protected function tearDown(): void
    {
        MockClient::destroyGlobal();

        parent::tearDown();
    }

    protected function getFixture(string $path): string
    {
        $fixturePath = __DIR__.'/Fixtures/Responses/'.$path;

        if (! file_exists($fixturePath)) {
            throw new \RuntimeException("Fixture not found: {$path}");
        }

        return (string) file_get_contents($fixturePath);
    }

    /**
     * @param  class-string<Request>  $requestClass
     * @param  array<string, mixed>|self::BODY_NOT_CHECKED|null  $body
     */
    protected function assertSentRequest(
        MockClient $mockClient,
        string $requestClass,
        Method $method,
        string $endpoint,
        array|string|null $body = self::BODY_NOT_CHECKED,
    ): void {
        $mockClient->assertSent(function (Request $request, Response $response) use ($requestClass, $method, $endpoint, $body): bool {
            expect($request)->toBeInstanceOf($requestClass);
            expect($request->getMethod())->toBe($method);
            expect($request->resolveEndpoint())->toBe($endpoint);

            if ($body !== self::BODY_NOT_CHECKED) {
                expect($response->getPendingRequest()->body()?->all() ?? [])->toBe($body ?? []);
            }

            return true;
        });
    }
}
