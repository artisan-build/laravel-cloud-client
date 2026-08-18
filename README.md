# Laravel Cloud Client

A complete PHP SDK for the Laravel Cloud API built with Saloon 3.x.

## Installation

```bash
composer require artisan-build/laravel-cloud-client
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=laravel-cloud-client-config
```

Set your API token in your `.env` file:

```env
LARAVEL_CLOUD_API_TOKEN=your-api-token
```

## Usage

### Using the Facade

```php
use ArtisanBuild\LaravelCloudClient\Facades\LaravelCloud;
use ArtisanBuild\LaravelCloudClient\Enums\SourceControlProviderType;

// List applications
$applications = LaravelCloud::applications()->list()->json('data');

// Create an application
$response = LaravelCloud::applications()->create(
    name: 'My App',
    repository: 'https://github.com/user/repo',
    region: 'us-east-1',
    sourceControlProviderType: SourceControlProviderType::GitHub,
);

// Trigger a deployment
$deployment = LaravelCloud::deployments()->trigger(environmentId: 'env-01k7env000000000000000001');
```

### Using Dependency Injection

```php
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;

class DeploymentController extends Controller
{
    public function __construct(
        protected LaravelCloudClient $cloud
    ) {}

    public function deploy(string $environmentId)
    {
        return $this->cloud->deployments()
            ->trigger($environmentId)
            ->json('data');
    }
}
```

### Standalone Usage

```php
use ArtisanBuild\LaravelCloudClient\LaravelCloudClient;

$client = new LaravelCloudClient(apiToken: 'your-token');

$apps = $client->applications()->list()->json('data');
```

## Available Resources

### Applications

```php
$client->applications()->list();
$client->applications()->create(name: 'App', repository: 'url', region: 'us-east-1', sourceControlProviderType: SourceControlProviderType::GitHub);
$client->applications()->get(applicationId: 'app-01k7app000000000000000001');
$client->applications()->update(applicationId: 'app-01k7app000000000000000001', name: 'New Name');
$client->applications()->delete(applicationId: 'app-01k7app000000000000000001');
```

### Environments

```php
$client->environments()->list(applicationId: 'app-01k7app000000000000000001');
$client->environments()->create(applicationId: 'app-01k7app000000000000000001', name: 'staging', branch: 'main');
$client->environments()->get(environmentId: 'env-01k7env000000000000000001');
$client->environments()->update(environmentId: 'env-01k7env000000000000000001', name: 'new-name');
$client->environments()->delete(environmentId: 'env-01k7env000000000000000001');
$client->environments()->refresh(environmentId: 'env-01k7env000000000000000001');

// Environment Variables
$client->environments()->listVariables(environmentId: 'env-01k7env000000000000000001');
$client->environments()->createVariable(environmentId: 'env-01k7env000000000000000001', key: 'API_KEY', value: 'secret');
$client->environments()->updateVariable(environmentId: 'env-01k7env000000000000000001', key: 'API_KEY', value: 'new-secret');
$client->environments()->deleteVariable(environmentId: 'env-01k7env000000000000000001', key: 'API_KEY');
```

### Deployments

```php
$client->deployments()->list(environmentId: 'env-01k7env000000000000000001');
$client->deployments()->trigger(environmentId: 'env-01k7env000000000000000001');
$client->deployments()->get(deploymentId: 'dep-01k7dep000000000000000001');
$client->deployments()->logs(deploymentId: 'dep-01k7dep000000000000000001');
$client->deployments()->cancel(deploymentId: 'dep-01k7dep000000000000000001');
```

### Instances

```php
$client->instances()->list(environmentId: 'env-01k7env000000000000000001');
$client->instances()->create(
    environmentId: 'env-01k7env000000000000000001',
    name: 'web',
    type: InstanceType::Service,
    size: InstanceSize::Flex512Mb,
    scalingType: InstanceScalingType::Custom,
    minReplicas: 1,
    visibilityTimeout: null,
    shutdownTimeout: null,
);
$client->instances()->get(instanceId: 'ins-01k7ins000000000000000001');
$client->instances()->update(instanceId: 'ins-01k7ins000000000000000001', size: InstanceSize::Flex1Gb);
$client->instances()->delete(instanceId: 'ins-01k7ins000000000000000001');
$client->instances()->restart(instanceId: 'ins-01k7ins000000000000000001');
```

### Domains

```php
$client->domains()->list(environmentId: 'env-01k7env000000000000000001');
$client->domains()->add(environmentId: 'env-01k7env000000000000000001', domain: 'example.com');
$client->domains()->get(domainId: 'dom-01k7dom000000000000000001');
$client->domains()->update(domainId: 'dom-01k7dom000000000000000001', domain: 'new-domain.com');
$client->domains()->delete(domainId: 'dom-01k7dom000000000000000001');
$client->domains()->requestSsl(domainId: 'dom-01k7dom000000000000000001');
$client->domains()->getSslStatus(domainId: 'dom-01k7dom000000000000000001');
```

### Database Clusters

```php
$client->databaseClusters()->list();
$client->databaseClusters()->create(type: DatabaseType::MySQL, name: 'main-db', region: 'us-east-1', config: ['size' => 'mysql-flex-512mb']);
$client->databaseClusters()->get(clusterId: 'db-01k7db000000000000000001');
$client->databaseClusters()->update(clusterId: 'db-01k7db000000000000000001', config: ['size' => 'mysql-flex-1gb']);
$client->databaseClusters()->delete(clusterId: 'db-01k7db000000000000000001');

// Databases within a cluster
$client->databaseClusters()->listDatabases(clusterId: 'db-01k7db000000000000000001');
$client->databaseClusters()->createDatabase(clusterId: 'db-01k7db000000000000000001', name: 'app_db');
$client->databaseClusters()->deleteDatabase(clusterId: 'db-01k7db000000000000000001', databaseName: 'old_db');

// Users within a cluster
$client->databaseClusters()->listUsers(clusterId: 'db-01k7db000000000000000001');
$client->databaseClusters()->createUser(clusterId: 'db-01k7db000000000000000001', username: 'app_user');
$client->databaseClusters()->deleteUser(clusterId: 'db-01k7db000000000000000001', username: 'old_user');
```

### Background Processes

```php
$client->backgroundProcesses()->list(instanceId: 'ins-01k7ins000000000000000001');
$client->backgroundProcesses()->create(instanceId: 'ins-01k7ins000000000000000001', command: 'php artisan queue:work', processes: 2, type: DaemonType::Custom);
$client->backgroundProcesses()->get(processId: 'bgp-01k7bgp000000000000000001');
$client->backgroundProcesses()->update(processId: 'bgp-01k7bgp000000000000000001', processes: 4);
$client->backgroundProcesses()->delete(processId: 'bgp-01k7bgp000000000000000001');
$client->backgroundProcesses()->restart(processId: 'bgp-01k7bgp000000000000000001');
```

### Commands

```php
$client->commands()->list(environmentId: 'env-01k7env000000000000000001');
$client->commands()->execute(environmentId: 'env-01k7env000000000000000001', command: 'php artisan migrate');
$client->commands()->get(commandId: 'cmd-01k7cmd000000000000000001');
$client->commands()->output(commandId: 'cmd-01k7cmd000000000000000001');
$client->commands()->cancel(commandId: 'cmd-01k7cmd000000000000000001');
```

### Object Storage

```php
$client->objectStorage()->listBuckets();
$client->objectStorage()->createBucket(name: 'uploads', visibility: 'private', jurisdiction: 'us', keyName: 'app-key', keyPermission: 'read_write');
$client->objectStorage()->getBucket(bucketId: 'bucket-01k7bucket0000000000001');
$client->objectStorage()->updateBucket(bucketId: 'bucket-01k7bucket0000000000001', public: true);
$client->objectStorage()->deleteBucket(bucketId: 'bucket-01k7bucket0000000000001');

// Access Keys
$client->objectStorage()->listAccessKeys(bucketId: 'bucket-01k7bucket0000000000001');
$client->objectStorage()->createAccessKey(bucketId: 'bucket-01k7bucket0000000000001', name: 'my-key', permission: 'read_write');
$client->objectStorage()->deleteAccessKey(bucketId: 'bucket-01k7bucket0000000000001', accessKeyId: 'key-01k7key000000000000000001');
```

### Caches

```php
$client->caches()->list();
$client->caches()->create(type: 'laravel_valkey', name: 'main-cache', region: 'us-east-1', size: CacheSize::ValkeyFlex250Mb, autoUpgradeEnabled: true, isPublic: false);
$client->caches()->get(cacheId: 'cache-01k7cache0000000000001');
$client->caches()->update(cacheId: 'cache-01k7cache0000000000001', size: CacheSize::ValkeyFlex1Gb);
$client->caches()->delete(cacheId: 'cache-01k7cache0000000000001');
$client->caches()->flush(cacheId: 'cache-01k7cache0000000000001');
```

## Known Unverified SDK Surfaces

These methods remain available for legacy or undocumented compatibility, but no published Laravel Cloud operation corresponds to their current calls. Do not rely on them for new provisioning work without live API verification.

Request bodies, documented enum values, and covered JSON:API response fixtures have been reconciled against the published OpenAPI for the spec-verified surfaces. Methods listed below remain undocumented compatibility surfaces and should not be treated as API conformance.

- `environments()->listVariables()` and `environments()->updateVariable()`: no published list/update operations exist. The published environment-variable operations are `add-environment-variables` and `delete-environment-variables`; the SDK's current list/update methods do not correspond to those operations.
- `commands()->output()` and `commands()->cancel()`: no published output or cancel operations exist. The published command operations are `list-commands`, `run-command`, and `get-command`; poll `get-command` for command state/output.
- `caches()->flush()`: no dedicated cache-resource flush operation exists. The published purge edge cache operation is environment-level and is a different operation.
- `databaseClusters()->listUsers()`, `databaseClusters()->createUser()`, and `databaseClusters()->deleteUser()`: no published database-user API exists. Published database operations cover clusters, databases, snapshots, and restores.
- `domains()->getSslStatus()`: no published SSL-certificate-status operation exists. Published domain operations manage domains themselves.
- `backgroundProcesses()->restart()`: no published background-process restart operation exists.
- `deployments()->cancel()`: no published deployment cancel operation exists.
- `environments()->refresh()`: no published environment refresh operation exists.
- `instances()->restart()`: no published instance restart operation exists.

## Artisan Commands

The package includes CLI commands for common operations:

```bash
# List applications
php artisan cloud:applications:list
php artisan cloud:applications:list --json

# Trigger deployment
php artisan cloud:deploy {environment}

# Manage environment variables
php artisan cloud:env:list {environment}
php artisan cloud:env:set {environment} {key} {value}
php artisan cloud:env:delete {environment} {key}

# Check deployment status
php artisan cloud:status {deployment}
php artisan cloud:logs {deployment}
```

## Error Handling

```php
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;

try {
    $response = $client->applications()->get('app-01k7app000000000000000001');
} catch (NotFoundException $e) {
    // Application not found
    $statusCode = $e->getStatusCode(); // 404
} catch (ValidationException $e) {
    // Validation errors
    $errors = $e->getResponseData()['errors'];
} catch (RateLimitException $e) {
    // Rate limited
    $retryAfter = $e->getRetryAfter(); // Seconds to wait
} catch (AuthenticationException $e) {
    // Invalid API token
}
```

## Enums

The SDK provides type-safe enums:

```php
use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Enums\DeploymentStatus;
use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;

// Database types
DatabaseType::MySQL;
DatabaseType::PostgreSQL;

// Instance types
InstanceType::Web;
InstanceType::Worker;
InstanceType::Scheduler;

// Deployment status with helper methods
$status = DeploymentStatus::Deploying;
$status->isInProgress(); // true
$status->isComplete();   // false
$status->isSuccessful(); // false

// Environment types
EnvironmentType::Production;
EnvironmentType::Staging;
EnvironmentType::Development;

// PHP versions
PhpVersion::Php81;
PhpVersion::Php82;
PhpVersion::Php83;
PhpVersion::Php84;
```

## Testing

```bash
composer test
```

## OpenAPI Spec

The SDK vendors Laravel Cloud's OpenAPI document at `resources/api-spec/api.json`. Contract tests read this local copy only, so CI is deterministic and never fetches the spec during the gate.

Refresh the vendored document deliberately when adopting upstream API changes:

```bash
composer refresh-api-spec
```

Review and commit the resulting `resources/api-spec/api.json` diff with the SDK changes it requires.

## Code Quality

```bash
composer quality
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
