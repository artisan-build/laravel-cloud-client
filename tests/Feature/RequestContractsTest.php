<?php

declare(strict_types=1);

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CacheType;
use ArtisanBuild\LaravelCloudClient\Enums\DaemonType;
use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Enums\DomainVerificationMethod;
use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentVariablesInsertMethod;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Enums\SourceControlProviderType;
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\CreateApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\DeleteApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\GetApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\ListApplications;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\UpdateApplication;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\CreateBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\DeleteBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\GetBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\ListBackgroundProcesses;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\RestartBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\UpdateBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\CreateCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\DeleteCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\GetCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\ListCaches;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\ListCacheTypes;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\UpdateCache;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\ExecuteCommand;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\GetCommand;
use ArtisanBuild\LaravelCloudClient\Requests\Commands\ListCommands;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\CreateDatabase;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\CreateDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\DeleteDatabase;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\DeleteDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\GetDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabaseClusters;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabases;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabaseTypes;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\UpdateDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\CancelDeployment;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\GetDeployment;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\GetDeploymentLogs;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\ListDeployments;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\TriggerDeployment;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\AddDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\DeleteDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\GetDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\ListDomains;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\RequestSslCertificate;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\UpdateDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\CreateEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\CreateEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\DeleteEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\DeleteEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\GetEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\ListEnvironments;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\RefreshEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\UpdateEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\CreateInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\DeleteInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\GetInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\ListInstances;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\ListInstanceSizes;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\RestartInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\UpdateInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\GetOrganization;
use ArtisanBuild\LaravelCloudClient\Requests\Meta\ListRegions;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\CreateBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\CreateBucketAccessKey;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\DeleteBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\DeleteBucketAccessKey;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\GetBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\ListBucketAccessKeys;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\ListBuckets;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\UpdateBucket;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

function cloudClientWithMock(MockClient $mockClient): LaravelCloudClient
{
    $client = new LaravelCloudClient(apiToken: 'test-token');
    $client->withMockClient($mockClient);

    return $client;
}

/**
 * @return array<string, array{0: Closure(LaravelCloudClient): Response, 1: class-string, 2: Method, 3: string, 4?: array<string, mixed>|string|null}>
 */
function specVerifiedRequestEndpointsAndMethods(): array
{
    $applicationId = 'app-01k7app000000000000000001';
    $environmentId = 'env-01k7env000000000000000001';
    $instanceId = 'ins-01k7ins000000000000000001';
    $processId = 'bgp-01k7bgp000000000000000001';
    $cacheId = 'cache-01k7cache0000000000001';
    $commandId = 'cmd-01k7cmd000000000000000001';
    $clusterId = 'db-01k7db000000000000000001';
    $deploymentId = 'dep-01k7dep000000000000000001';
    $domainId = 'dom-01k7dom000000000000000001';
    $bucketId = 'bucket-01k7bucket0000000000001';
    $bodyNotChecked = ArtisanBuild\LaravelCloudClient\Tests\TestCase::BODY_NOT_CHECKED;

    return [
        'applications list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->applications()->list(), ListApplications::class, Method::GET, '/applications', null],
        'applications create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->applications()->create('New Application', 'https://github.com/user/new-repo', 'us-east-1', SourceControlProviderType::GitHub, clusterId: 'cluster-01k7cluster0000000001'), CreateApplication::class, Method::POST, '/applications', ['name' => 'New Application', 'repository' => 'https://github.com/user/new-repo', 'region' => 'us-east-1', 'source_control_provider_type' => 'github', 'cluster_id' => 'cluster-01k7cluster0000000001']],
        'applications get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->applications()->get($applicationId), GetApplication::class, Method::GET, '/applications/'.$applicationId, null],
        'applications update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->applications()->update($applicationId, name: 'Updated Application', sourceControlProviderType: SourceControlProviderType::GitLab), UpdateApplication::class, Method::PATCH, '/applications/'.$applicationId, ['name' => 'Updated Application', 'source_control_provider_type' => 'gitlab']],
        'applications delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->applications()->delete($applicationId), DeleteApplication::class, Method::DELETE, '/applications/'.$applicationId, null],

        'background processes list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->list($instanceId), ListBackgroundProcesses::class, Method::GET, '/instances/'.$instanceId.'/background-processes', null],
        'background processes create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->create($instanceId, 'php artisan queue:work', 2, DaemonType::Custom), CreateBackgroundProcess::class, Method::POST, '/instances/'.$instanceId.'/background-processes', ['type' => 'custom', 'command' => 'php artisan queue:work', 'processes' => 2]],
        'background processes get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->get($processId), GetBackgroundProcess::class, Method::GET, '/background-processes/'.$processId, null],
        'background processes update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->update($processId, processes: 4, type: DaemonType::Custom), UpdateBackgroundProcess::class, Method::PATCH, '/background-processes/'.$processId, ['type' => 'custom', 'processes' => 4]],
        'background processes delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->delete($processId), DeleteBackgroundProcess::class, Method::DELETE, '/background-processes/'.$processId, null],

        'caches list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->list(), ListCaches::class, Method::GET, '/caches', null],
        'caches create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->create(CacheType::LaravelValkey, 'new-cache', 'us-east-1', CacheSize::ValkeyFlex250Mb, true, false, $clusterId), CreateCache::class, Method::POST, '/caches', ['type' => 'laravel_valkey', 'name' => 'new-cache', 'region' => 'us-east-1', 'size' => 'valkey-flex-250mb', 'auto_upgrade_enabled' => true, 'is_public' => false, 'cluster_id' => $clusterId]],
        'caches types endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->types(), ListCacheTypes::class, Method::GET, '/caches/types', null],
        'caches get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->get($cacheId), GetCache::class, Method::GET, '/caches/'.$cacheId, null],
        'caches update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->update($cacheId, size: CacheSize::ValkeyFlex1Gb), UpdateCache::class, Method::PATCH, '/caches/'.$cacheId, ['size' => 'valkey-flex-1gb']],
        'caches delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->caches()->delete($cacheId), DeleteCache::class, Method::DELETE, '/caches/'.$cacheId, null],

        'commands list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->commands()->list($environmentId), ListCommands::class, Method::GET, '/environments/'.$environmentId.'/commands', null],
        'commands run endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->commands()->execute($environmentId, 'php artisan migrate'), ExecuteCommand::class, Method::POST, '/environments/'.$environmentId.'/commands', ['command' => 'php artisan migrate']],
        'commands get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->commands()->get($commandId), GetCommand::class, Method::GET, '/commands/'.$commandId, null],

        'database clusters list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->list(), ListDatabaseClusters::class, Method::GET, '/databases/clusters', null],
        'database clusters create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->create(DatabaseType::LaravelMySql84, 'main-db', 'us-east-1', ['size' => 'mysql-flex-512mb'], $clusterId), CreateDatabaseCluster::class, Method::POST, '/databases/clusters', ['type' => 'laravel_mysql_84', 'name' => 'main-db', 'region' => 'us-east-1', 'config' => ['size' => 'mysql-flex-512mb'], 'cluster_id' => $clusterId]],
        'database types endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->types(), ListDatabaseTypes::class, Method::GET, '/databases/types', null],
        'database clusters get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->get($clusterId), GetDatabaseCluster::class, Method::GET, '/databases/clusters/'.$clusterId, null],
        'database clusters update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->update($clusterId, ['size' => 'mysql-flex-1gb']), UpdateDatabaseCluster::class, Method::PATCH, '/databases/clusters/'.$clusterId, ['config' => ['size' => 'mysql-flex-1gb']]],
        'database clusters delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->delete($clusterId), DeleteDatabaseCluster::class, Method::DELETE, '/databases/clusters/'.$clusterId, null],
        'databases list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->listDatabases($clusterId), ListDatabases::class, Method::GET, '/databases/clusters/'.$clusterId.'/databases', null],
        'databases create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->createDatabase($clusterId, 'new-db'), CreateDatabase::class, Method::POST, '/databases/clusters/'.$clusterId.'/databases', ['name' => 'new-db']],
        'databases delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->databaseClusters()->deleteDatabase($clusterId, 'schema-01k7schema0000000000001'), DeleteDatabase::class, Method::DELETE, '/databases/clusters/'.$clusterId.'/databases/schema-01k7schema0000000000001', null],

        'deployments list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->deployments()->list($environmentId), ListDeployments::class, Method::GET, '/environments/'.$environmentId.'/deployments', null],
        'deployments initiate endpoint has no body and is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->deployments()->trigger($environmentId), TriggerDeployment::class, Method::POST, '/environments/'.$environmentId.'/deployments', null],
        'deployments get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->deployments()->get($deploymentId), GetDeployment::class, Method::GET, '/deployments/'.$deploymentId, null],
        'deployments logs endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->deployments()->logs($deploymentId), GetDeploymentLogs::class, Method::GET, '/deployments/'.$deploymentId.'/logs', null],

        'domains list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->list($environmentId), ListDomains::class, Method::GET, '/environments/'.$environmentId.'/domains', null],
        'domains add endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->add($environmentId, 'example.com'), AddDomain::class, Method::POST, '/environments/'.$environmentId.'/domains', ['name' => 'example.com']],
        'domains get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->get($domainId), GetDomain::class, Method::GET, '/domains/'.$domainId, null],
        'domains update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->update($domainId, DomainVerificationMethod::RealTime), UpdateDomain::class, Method::PATCH, '/domains/'.$domainId, ['verification_method' => 'real_time']],
        'domains delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->delete($domainId), DeleteDomain::class, Method::DELETE, '/domains/'.$domainId, null],
        'domains verify endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->domains()->requestSsl($domainId), RequestSslCertificate::class, Method::POST, '/domains/'.$domainId.'/verify', null],

        'environments list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->list($applicationId), ListEnvironments::class, Method::GET, '/applications/'.$applicationId.'/environments', null],
        'environments create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->create($applicationId, 'production', 'main'), CreateEnvironment::class, Method::POST, '/applications/'.$applicationId.'/environments', ['name' => 'production', 'branch' => 'main']],
        'environments get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->get($environmentId), GetEnvironment::class, Method::GET, '/environments/'.$environmentId, null],
        'environment update attach endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->update($environmentId, cacheId: 'cache-01k7cache0000000000001'), UpdateEnvironment::class, Method::PATCH, '/environments/'.$environmentId, ['cache_id' => 'cache-01k7cache0000000000001']],
        'environments delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->delete($environmentId), DeleteEnvironment::class, Method::DELETE, '/environments/'.$environmentId, null],
        'environment variables add endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->addVariables($environmentId, [['key' => 'API_KEY', 'value' => 'secret']], EnvironmentVariablesInsertMethod::Set), CreateEnvironmentVariable::class, Method::POST, '/environments/'.$environmentId.'/variables', ['method' => 'set', 'variables' => [['key' => 'API_KEY', 'value' => 'secret']]]],
        'environment variables delete endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->environments()->deleteVariables($environmentId, ['API_KEY']), DeleteEnvironmentVariable::class, Method::POST, '/environments/'.$environmentId.'/variables/delete', ['keys' => ['API_KEY']]],

        'instances list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->list($environmentId), ListInstances::class, Method::GET, '/environments/'.$environmentId.'/instances', null],
        'instances create endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->create($environmentId, 'web', InstanceType::Service, InstanceSize::Flex512Mb, InstanceScalingType::Custom, 1, null, null, 2), CreateInstance::class, Method::POST, '/environments/'.$environmentId.'/instances', ['name' => 'web', 'type' => 'service', 'size' => 'flex-512mb', 'scaling_type' => 'custom', 'min_replicas' => 1, 'visibility_timeout' => null, 'shutdown_timeout' => null, 'max_replicas' => 2]],
        'instances create sends uses_scheduler when asked to' => [fn (LaravelCloudClient $client): Response => $client->instances()->create($environmentId, 'web', InstanceType::Service, InstanceSize::Flex512Mb, InstanceScalingType::Custom, 1, null, null, 2, true), CreateInstance::class, Method::POST, '/environments/'.$environmentId.'/instances', ['name' => 'web', 'type' => 'service', 'size' => 'flex-512mb', 'scaling_type' => 'custom', 'min_replicas' => 1, 'visibility_timeout' => null, 'shutdown_timeout' => null, 'max_replicas' => 2, 'uses_scheduler' => true]],
        'instances sizes endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->sizes(), ListInstanceSizes::class, Method::GET, '/instances/sizes', null],
        'instances get endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->get($instanceId), GetInstance::class, Method::GET, '/instances/'.$instanceId, null],
        'instances update endpoint and body are spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->update($instanceId, size: InstanceSize::Flex512Mb), UpdateInstance::class, Method::PATCH, '/instances/'.$instanceId, ['size' => 'flex-512mb']],
        'instances update sends uses_scheduler when asked to' => [fn (LaravelCloudClient $client): Response => $client->instances()->update($instanceId, usesScheduler: true), UpdateInstance::class, Method::PATCH, '/instances/'.$instanceId, ['uses_scheduler' => true]],
        'instances delete endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->instances()->delete($instanceId), DeleteInstance::class, Method::DELETE, '/instances/'.$instanceId, null],

        'meta organization endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->meta()->organization(), GetOrganization::class, Method::GET, '/meta/organization', $bodyNotChecked],
        'meta regions endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->meta()->regions(), ListRegions::class, Method::GET, '/meta/regions', $bodyNotChecked],
        'object storage access keys list endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->listAccessKeys($bucketId), ListBucketAccessKeys::class, Method::GET, '/buckets/'.$bucketId.'/keys', $bodyNotChecked],
        'object storage access keys create endpoint is spec-verified' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->createAccessKey($bucketId, 'new-key', 'read_write'), CreateBucketAccessKey::class, Method::POST, '/buckets/'.$bucketId.'/keys', $bodyNotChecked],
    ];
}

/**
 * @return array<string, array{0: Closure(LaravelCloudClient): Response, 1: class-string, 2: Method, 3: string, 4?: array<string, mixed>|string|null}>
 */
function currentRequestShapesNotYetSpecVerified(): array
{
    $applicationId = 'app-01k7app000000000000000001';
    $environmentId = 'env-01k7env000000000000000001';
    $instanceId = 'ins-01k7ins000000000000000001';
    $processId = 'bgp-01k7bgp000000000000000001';
    $cacheId = 'cache-01k7cache0000000000001';
    $commandId = 'cmd-01k7cmd000000000000000001';
    $clusterId = 'db-01k7db000000000000000001';
    $deploymentId = 'dep-01k7dep000000000000000001';
    $domainId = 'dom-01k7dom000000000000000001';
    $bucketId = 'bucket-01k7bucket0000000000001';
    $accessKeyId = 'key-01k7key000000000000000001';

    return [
        'background processes restart current shape only' => [fn (LaravelCloudClient $client): Response => $client->backgroundProcesses()->restart($processId), RestartBackgroundProcess::class, Method::POST, '/background-processes/'.$processId.'/restart', null],
        'deployments cancel current shape only' => [fn (LaravelCloudClient $client): Response => $client->deployments()->cancel($deploymentId), CancelDeployment::class, Method::DELETE, '/deployments/'.$deploymentId, null],
        'environments refresh current shape only' => [fn (LaravelCloudClient $client): Response => $client->environments()->refresh($environmentId), RefreshEnvironment::class, Method::POST, '/environments/'.$environmentId.'/refresh', null],
        'instances restart current shape only' => [fn (LaravelCloudClient $client): Response => $client->instances()->restart($instanceId), RestartInstance::class, Method::POST, '/instances/'.$instanceId.'/restart', null],

        'object storage buckets list current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->listBuckets(), ListBuckets::class, Method::GET, '/buckets', null],
        'object storage buckets create current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->createBucket('new-bucket', 'private', 'us', 'primary-key', 'read_write'), CreateBucket::class, Method::POST, '/buckets', ['name' => 'new-bucket', 'visibility' => 'private', 'jurisdiction' => 'us', 'key_name' => 'primary-key', 'key_permission' => 'read_write']],
        'object storage buckets get current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->getBucket($bucketId), GetBucket::class, Method::GET, '/buckets/'.$bucketId, null],
        'object storage buckets update current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->updateBucket($bucketId, name: 'renamed-bucket'), UpdateBucket::class, Method::PATCH, '/buckets/'.$bucketId, ['name' => 'renamed-bucket']],
        'object storage buckets delete current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->deleteBucket($bucketId), DeleteBucket::class, Method::DELETE, '/buckets/'.$bucketId, null],
        'object storage access keys delete current shape only' => [fn (LaravelCloudClient $client): Response => $client->objectStorage()->deleteAccessKey($bucketId, $accessKeyId), DeleteBucketAccessKey::class, Method::DELETE, '/bucket-keys/'.$accessKeyId, null],
    ];
}

it('sends spec-verified request endpoints, methods, and checked bodies', function (Closure $send, string $requestClass, Method $method, string $endpoint, array|string|null $body = ArtisanBuild\LaravelCloudClient\Tests\TestCase::BODY_NOT_CHECKED): void {
    $mockClient = new MockClient([
        MockResponse::make(body: json_encode(['data' => []]), status: 200),
    ]);

    $send(cloudClientWithMock($mockClient));

    $this->assertSentRequest($mockClient, $requestClass, $method, $endpoint, $body);
})->with(specVerifiedRequestEndpointsAndMethods());

it('pins current SDK request shapes that are NOT yet spec-verified and must not be treated as API conformance', function (Closure $send, string $requestClass, Method $method, string $endpoint, array|string|null $body = ArtisanBuild\LaravelCloudClient\Tests\TestCase::BODY_NOT_CHECKED): void {
    $mockClient = new MockClient([
        MockResponse::make(body: json_encode(['data' => []]), status: 200),
    ]);

    $send(cloudClientWithMock($mockClient));

    $this->assertSentRequest($mockClient, $requestClass, $method, $endpoint, $body);
})->with(currentRequestShapesNotYetSpecVerified());

/*
 * The enums are a snapshot of the API's vocabulary, not its definition. Cloud
 * adds sizes and engines whenever it likes, and an interface reading the LIVE
 * list will offer one of those long before this package is released again —
 * so the request classes take a raw string as readily as a case, and what
 * reaches the wire has to be identical either way.
 */
it('sends a live option value the bundled enums do not model', function (): void {
    $mockClient = new MockClient([
        MockResponse::make(body: json_encode(['data' => []]), status: 200),
    ]);

    $client = cloudClientWithMock($mockClient);

    $client->caches()->create('some_future_engine', 'new-cache', 'us-east-1', 'future-4tb', true, false);

    $this->assertSentRequest($mockClient, CreateCache::class, Method::POST, '/caches', [
        'type' => 'some_future_engine',
        'name' => 'new-cache',
        'region' => 'us-east-1',
        'size' => 'future-4tb',
        'auto_upgrade_enabled' => true,
        'is_public' => false,
    ]);
});

it('sends a live database type and instance size the bundled enums do not model', function (): void {
    $mockClient = new MockClient([
        MockResponse::make(body: json_encode(['data' => []]), status: 200),
    ]);

    $client = cloudClientWithMock($mockClient);

    $client->databaseClusters()->create('neon_serverless_postgres_19', 'main-db', 'us-east-1', ['cu_min' => 0.25]);

    $this->assertSentRequest($mockClient, CreateDatabaseCluster::class, Method::POST, '/databases/clusters', [
        'type' => 'neon_serverless_postgres_19',
        'name' => 'main-db',
        'region' => 'us-east-1',
        'config' => ['cu_min' => 0.25],
    ]);

    $mockClient = new MockClient([
        MockResponse::make(body: json_encode(['data' => []]), status: 200),
    ]);

    $client = cloudClientWithMock($mockClient);

    // `min_replicas` is passed as NULL and must not reach the body. This
    // fixture used to pass 0 and assert `'min_replicas' => 0`, which is exactly
    // the body Cloud rejects — the schema says the field is "rejected when used
    // with `auto`" and "not applicable to managed queues". A fixture agreeing
    // with the code and disagreeing with the spec is what let this ship.
    $client->instances()->create('env-01k7env000000000000000001', 'queue', InstanceType::ManagedQueue, 'mq.pro.32gb', InstanceScalingType::Auto, null, null, null);

    $this->assertSentRequest($mockClient, CreateInstance::class, Method::POST, '/environments/env-01k7env000000000000000001/instances', [
        'name' => 'queue',
        'type' => 'managed_queue',
        'size' => 'mq.pro.32gb',
        'scaling_type' => 'auto',
        'visibility_timeout' => null,
        'shutdown_timeout' => null,
    ]);
});
