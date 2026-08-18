<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\DaemonType;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\CreateBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\DeleteBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\GetBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\ListBackgroundProcesses;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\RestartBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses\UpdateBackgroundProcess;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing background processes.
 */
final class BackgroundProcessesResource extends Resource
{
    /**
     * List all background processes for an instance.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $instanceId): Response
    {
        return $this->send(new ListBackgroundProcesses(instanceId: $instanceId));
    }

    /**
     * Create a new background process.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(string $instanceId, string $command, ?int $processes = null, DaemonType $type = DaemonType::Custom): Response
    {
        return $this->send(new CreateBackgroundProcess(
            instanceId: $instanceId,
            type: $type,
            command: $command,
            processes: $processes ?? 1,
        ));
    }

    /**
     * Get a specific background process by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $processId): Response
    {
        return $this->send(new GetBackgroundProcess(processId: $processId));
    }

    /**
     * Update an existing background process.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(string $processId, ?string $command = null, ?int $processes = null, ?DaemonType $type = null): Response
    {
        return $this->send(new UpdateBackgroundProcess(
            processId: $processId,
            type: $type,
            command: $command,
            processes: $processes,
        ));
    }

    /**
     * Delete a background process.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $processId): Response
    {
        return $this->send(new DeleteBackgroundProcess(processId: $processId));
    }

    /**
     * Restart a background process.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function restart(string $processId): Response
    {
        return $this->send(new RestartBackgroundProcess(processId: $processId));
    }
}
