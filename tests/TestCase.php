<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Tests;

use ArtisanBuild\LaravelCloudClient\Providers\LaravelCloudServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
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
