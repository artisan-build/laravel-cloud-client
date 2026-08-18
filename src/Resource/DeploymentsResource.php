<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\CancelDeployment;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\GetDeployment;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\GetDeploymentLogs;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\ListDeployments;
use ArtisanBuild\LaravelCloudClient\Requests\Deployments\TriggerDeployment;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing deployments.
 */
final class DeploymentsResource extends Resource
{
    /**
     * List all deployments for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $environmentId): Response
    {
        return $this->send(new ListDeployments(environmentId: $environmentId));
    }

    /**
     * Trigger a new deployment for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function trigger(string $environmentId): Response
    {
        return $this->send(new TriggerDeployment(
            environmentId: $environmentId,
        ));
    }

    /**
     * Get a specific deployment by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $deploymentId): Response
    {
        return $this->send(new GetDeployment(deploymentId: $deploymentId));
    }

    /**
     * Get logs for a deployment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function logs(string $deploymentId): Response
    {
        return $this->send(new GetDeploymentLogs(deploymentId: $deploymentId));
    }

    /**
     * Cancel a running deployment.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function cancel(string $deploymentId): Response
    {
        return $this->send(new CancelDeployment(deploymentId: $deploymentId));
    }
}
