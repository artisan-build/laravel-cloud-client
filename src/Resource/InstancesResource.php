<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\CreateInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\DeleteInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\GetInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\ListInstances;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\RestartInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\UpdateInstance;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing instances.
 */
final class InstancesResource extends Resource
{
    /**
     * List all instances for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $environmentId): Response
    {
        return $this->send(new ListInstances(environmentId: $environmentId));
    }

    /**
     * Create a new instance (scale up).
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(
        string $environmentId,
        string $name,
        InstanceType $type,
        InstanceSize $size,
        InstanceScalingType $scalingType,
        int $minReplicas,
        ?int $visibilityTimeout,
        ?int $shutdownTimeout,
        ?int $maxReplicas = null,
    ): Response {
        return $this->send(new CreateInstance(
            environmentId: $environmentId,
            name: $name,
            type: $type,
            size: $size,
            scalingType: $scalingType,
            minReplicas: $minReplicas,
            visibilityTimeout: $visibilityTimeout,
            shutdownTimeout: $shutdownTimeout,
            maxReplicas: $maxReplicas,
        ));
    }

    /**
     * Get a specific instance by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $instanceId): Response
    {
        return $this->send(new GetInstance(instanceId: $instanceId));
    }

    /**
     * Update an existing instance.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(string $instanceId, ?InstanceSize $size = null): Response
    {
        return $this->send(new UpdateInstance(
            instanceId: $instanceId,
            size: $size,
        ));
    }

    /**
     * Delete an instance (scale down).
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $instanceId): Response
    {
        return $this->send(new DeleteInstance(instanceId: $instanceId));
    }

    /**
     * Restart an instance.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function restart(string $instanceId): Response
    {
        return $this->send(new RestartInstance(instanceId: $instanceId));
    }
}
