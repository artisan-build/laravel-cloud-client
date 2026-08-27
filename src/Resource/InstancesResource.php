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
use ArtisanBuild\LaravelCloudClient\Requests\Instances\ListInstanceSizes;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\RestartInstance;
use ArtisanBuild\LaravelCloudClient\Requests\Instances\UpdateInstance;
use ArtisanBuild\LaravelCloudClient\Resource;
use ArtisanBuild\LaravelCloudClient\Support\BackgroundProcess;
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
     * @param  list<BackgroundProcess>  $backgroundProcesses  the workers the instance runs; empty omits the key
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
        InstanceSize|string $size,
        InstanceScalingType $scalingType,
        // Nullable, and null OMITS it from the body — see CreateInstance. A
        // `custom` service instance still sends one; an `auto` one and a
        // managed queue must not, and a non-nullable type here left no way to
        // say so.
        ?int $minReplicas,
        ?int $visibilityTimeout,
        ?int $shutdownTimeout,
        ?int $maxReplicas = null,
        ?bool $usesScheduler = null,
        // BackgroundProcess objects rather than an array of arrays, so the
        // rules that only exist in the schema's prose — no `command` on a
        // worker, `processes` within 1-10 — are enforced here instead of
        // arriving as a 422 against a resource the customer is already paying
        // for. Cloud REQUIRES an entry on a managed queue.
        array $backgroundProcesses = [],
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
            usesScheduler: $usesScheduler,
            backgroundProcesses: array_values($backgroundProcesses),
        ));
    }

    /**
     * List the available instance sizes, grouped into `general` and
     * `managed_queue`.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function sizes(): Response
    {
        return $this->send(new ListInstanceSizes);
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
    public function update(string $instanceId, InstanceSize|string|null $size = null, ?bool $usesScheduler = null): Response
    {
        return $this->send(new UpdateInstance(
            instanceId: $instanceId,
            size: $size,
            usesScheduler: $usesScheduler,
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
