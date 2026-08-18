<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Resource\ApplicationsResource;
use ArtisanBuild\LaravelCloudClient\Resource\BackgroundProcessesResource;
use ArtisanBuild\LaravelCloudClient\Resource\CachesResource;
use ArtisanBuild\LaravelCloudClient\Resource\CommandsResource;
use ArtisanBuild\LaravelCloudClient\Resource\DatabaseClustersResource;
use ArtisanBuild\LaravelCloudClient\Resource\DeploymentsResource;
use ArtisanBuild\LaravelCloudClient\Resource\DomainsResource;
use ArtisanBuild\LaravelCloudClient\Resource\EnvironmentsResource;
use ArtisanBuild\LaravelCloudClient\Resource\InstancesResource;
use ArtisanBuild\LaravelCloudClient\Resource\MetaResource;
use ArtisanBuild\LaravelCloudClient\Resource\ObjectStorageResource;

it('resolves api token from constructor', function () {
    $client = new LaravelCloudClient(apiToken: 'constructor-token');

    expect($client)->toBeInstanceOf(LaravelCloudClient::class);
});

it('resolves api token from config', function () {
    config(['laravel-cloud-client.api_token' => 'config-token']);

    $client = new LaravelCloudClient;

    expect($client)->toBeInstanceOf(LaravelCloudClient::class);
});

it('throws exception when no api token available', function () {
    config(['laravel-cloud-client.api_token' => null]);

    new LaravelCloudClient;
})->throws(AuthenticationException::class, 'No API token provided');

it('resolves base url from constructor', function () {
    $client = new LaravelCloudClient(
        apiToken: 'test-token',
        baseUrl: 'https://custom.api.com'
    );

    expect($client->resolveBaseUrl())->toBe('https://custom.api.com');
});

it('uses default base url when none provided', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->resolveBaseUrl())->toBe('https://cloud.laravel.com/api');
});

it('returns applications resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->applications())->toBeInstanceOf(ApplicationsResource::class);
});

it('returns environments resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->environments())->toBeInstanceOf(EnvironmentsResource::class);
});

it('returns deployments resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->deployments())->toBeInstanceOf(DeploymentsResource::class);
});

it('returns instances resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->instances())->toBeInstanceOf(InstancesResource::class);
});

it('returns domains resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->domains())->toBeInstanceOf(DomainsResource::class);
});

it('returns database clusters resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->databaseClusters())->toBeInstanceOf(DatabaseClustersResource::class);
});

it('returns background processes resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->backgroundProcesses())->toBeInstanceOf(BackgroundProcessesResource::class);
});

it('returns commands resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->commands())->toBeInstanceOf(CommandsResource::class);
});

it('returns object storage resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->objectStorage())->toBeInstanceOf(ObjectStorageResource::class);
});

it('returns caches resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->caches())->toBeInstanceOf(CachesResource::class);
});

it('returns meta resource', function () {
    $client = new LaravelCloudClient(apiToken: 'test-token');

    expect($client->meta())->toBeInstanceOf(MetaResource::class);
});
