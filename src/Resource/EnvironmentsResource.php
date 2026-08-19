<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentVariablesInsertMethod;
use ArtisanBuild\LaravelCloudClient\Enums\PhpVersion;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\CreateEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\CreateEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\DeleteEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\DeleteEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\GetEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\ListEnvironments;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\ListEnvironmentVariables;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\RefreshEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\UpdateEnvironment;
use ArtisanBuild\LaravelCloudClient\Requests\Environments\UpdateEnvironmentVariable;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing environments.
 */
final class EnvironmentsResource extends Resource
{
    private const UNSET = '__laravel_cloud_unset__';

    /**
     * List all environments for an application.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $applicationId): Response
    {
        return $this->send(new ListEnvironments(applicationId: $applicationId));
    }

    /**
     * Create a new environment for an application.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(
        string $applicationId,
        string $name,
        string $branch,
        ?string $clusterId = null,
    ): Response {
        return $this->send(new CreateEnvironment(
            applicationId: $applicationId,
            name: $name,
            branch: $branch,
            clusterId: $clusterId,
        ));
    }

    /**
     * Get a specific environment by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $environmentId): Response
    {
        return $this->send(new GetEnvironment(environmentId: $environmentId));
    }

    /**
     * Update an existing environment.
     *
     * Resource attachment fields use PATCH semantics: omitted values leave the existing attachment unchanged,
     * null or an empty string detaches database/cache resources, and a string id attaches that resource.
     * Passing an empty array for filesystem keys detaches every bucket from the environment.
     *
     * @param  array<int, array{id: string, disk: string, is_default_disk: bool}>|null  $filesystemKeys
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(
        string $environmentId,
        string $name = self::UNSET,
        string $branch = self::UNSET,
        ?string $databaseSchemaId = self::UNSET,
        ?string $cacheId = self::UNSET,
        ?array $filesystemKeys = null,
        ?PhpVersion $phpVersion = null,
    ): Response {
        return $this->send(new UpdateEnvironment(
            environmentId: $environmentId,
            name: $name,
            branch: $branch,
            databaseSchemaId: $databaseSchemaId,
            cacheId: $cacheId,
            filesystemKeys: $filesystemKeys,
            phpVersion: $phpVersion,
        ));
    }

    /**
     * Delete an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $environmentId): Response
    {
        return $this->send(new DeleteEnvironment(environmentId: $environmentId));
    }

    /**
     * Refresh (restart services) for an environment.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function refresh(string $environmentId): Response
    {
        return $this->send(new RefreshEnvironment(environmentId: $environmentId));
    }

    /**
     * List all environment variables for an environment.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * The published environment-variable operations are add-environment-variables and delete-environment-variables.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function listVariables(string $environmentId): Response
    {
        return $this->send(new ListEnvironmentVariables(environmentId: $environmentId));
    }

    /**
     * Add environment variables to an environment.
     *
     * @param  non-empty-list<array{key: string, value: string}>  $variables
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function addVariables(
        string $environmentId,
        array $variables,
        EnvironmentVariablesInsertMethod $method = EnvironmentVariablesInsertMethod::Set,
    ): Response {
        return $this->send(new CreateEnvironmentVariable(
            environmentId: $environmentId,
            insertMethod: $method,
            variables: $variables,
        ));
    }

    /**
     * Create or update one environment variable.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function createVariable(string $environmentId, string $key, string $value): Response
    {
        return $this->addVariables($environmentId, [['key' => $key, 'value' => $value]]);
    }

    /**
     * Update an existing environment variable.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * The published environment-variable operations are add-environment-variables and delete-environment-variables.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function updateVariable(string $environmentId, string $key, string $value): Response
    {
        return $this->send(new UpdateEnvironmentVariable(
            environmentId: $environmentId,
            key: $key,
            value: $value,
        ));
    }

    /**
     * Delete an environment variable.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteVariable(string $environmentId, string $key): Response
    {
        return $this->deleteVariables($environmentId, [$key]);
    }

    /**
     * Delete environment variables by key.
     *
     * @param  non-empty-list<string>  $keys
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteVariables(string $environmentId, array $keys): Response
    {
        return $this->send(new DeleteEnvironmentVariable(
            environmentId: $environmentId,
            keys: $keys,
        ));
    }
}
