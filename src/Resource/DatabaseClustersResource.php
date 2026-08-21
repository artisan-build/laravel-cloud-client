<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\CreateDatabase;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\CreateDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\CreateDatabaseUser;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\DeleteDatabase;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\DeleteDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\DeleteDatabaseUser;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\GetDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabaseClusters;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabaseTypes;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabases;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\ListDatabaseUsers;
use ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters\UpdateDatabaseCluster;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;
use SensitiveParameter;

/**
 * Resource for managing database clusters.
 */
final class DatabaseClustersResource extends Resource
{
    /**
     * List all database clusters for an environment.
     *
     * Pass `databases` as the include to have each cluster carry the schemas
     * that belong to it; without one, Cloud returns no relationships at all.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(?string $include = null): Response
    {
        return $this->send(new ListDatabaseClusters(include: $include));
    }

    /**
     * List the database types available when creating a cluster, each with
     * the regions it runs in and the config schema its creation call expects.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function types(): Response
    {
        return $this->send(new ListDatabaseTypes);
    }

    /**
     * Create a new database cluster.
     *
     * @param  array<string, mixed>  $config
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(
        DatabaseType|string $type,
        string $name,
        string $region,
        array $config,
        ?string $clusterId = null,
    ): Response {
        return $this->send(new CreateDatabaseCluster(
            type: $type,
            name: $name,
            region: $region,
            databaseConfig: $config,
            clusterId: $clusterId,
        ));
    }

    /**
     * Get a specific database cluster by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $clusterId): Response
    {
        return $this->send(new GetDatabaseCluster(clusterId: $clusterId));
    }

    /**
     * Update an existing database cluster.
     *
     * @param  array<string, mixed>  $config
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(string $clusterId, array $config): Response
    {
        return $this->send(new UpdateDatabaseCluster(
            clusterId: $clusterId,
            databaseConfig: $config,
        ));
    }

    /**
     * Delete a database cluster.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $clusterId): Response
    {
        return $this->send(new DeleteDatabaseCluster(clusterId: $clusterId));
    }

    /**
     * List all databases in a cluster.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function listDatabases(string $clusterId): Response
    {
        return $this->send(new ListDatabases(clusterId: $clusterId));
    }

    /**
     * Create a new database in a cluster.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function createDatabase(string $clusterId, string $name): Response
    {
        return $this->send(new CreateDatabase(
            clusterId: $clusterId,
            name: $name,
        ));
    }

    /**
     * Delete a database from a cluster.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteDatabase(string $clusterId, string $databaseName): Response
    {
        return $this->send(new DeleteDatabase(
            clusterId: $clusterId,
            databaseName: $databaseName,
        ));
    }

    /**
     * List all users in a database cluster.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * Published database operations cover clusters, databases, snapshots, and restores, not database users.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function listUsers(string $clusterId): Response
    {
        return $this->send(new ListDatabaseUsers(clusterId: $clusterId));
    }

    /**
     * Create a new user in a database cluster.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * Published database operations cover clusters, databases, snapshots, and restores, not database users.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function createUser(string $clusterId, string $username, #[SensitiveParameter] ?string $password = null): Response
    {
        return $this->send(new CreateDatabaseUser(
            clusterId: $clusterId,
            username: $username,
            password: $password,
        ));
    }

    /**
     * Delete a user from a database cluster.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * Published database operations cover clusters, databases, snapshots, and restores, not database users.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteUser(string $clusterId, string $username): Response
    {
        return $this->send(new DeleteDatabaseUser(
            clusterId: $clusterId,
            username: $username,
        ));
    }
}
