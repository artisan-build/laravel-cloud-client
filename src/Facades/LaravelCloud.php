<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Facades;

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
use ArtisanBuild\LaravelCloudClient\Resource\ObjectStorageResource;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Laravel Cloud SDK.
 *
 * @method static ApplicationsResource applications()
 * @method static EnvironmentsResource environments()
 * @method static DeploymentsResource deployments()
 * @method static InstancesResource instances()
 * @method static DomainsResource domains()
 * @method static DatabaseClustersResource databaseClusters()
 * @method static BackgroundProcessesResource backgroundProcesses()
 * @method static CommandsResource commands()
 * @method static ObjectStorageResource objectStorage()
 * @method static CachesResource caches()
 *
 * @see LaravelCloudClient
 */
final class LaravelCloud extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelCloudClient::class;
    }
}
