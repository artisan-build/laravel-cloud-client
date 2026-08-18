<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\SourceControlProviderType;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\CreateApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\DeleteApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\GetApplication;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\ListApplications;
use ArtisanBuild\LaravelCloudClient\Requests\Applications\UpdateApplication;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing applications.
 */
final class ApplicationsResource extends Resource
{
    private const OMITTED = UpdateApplication::OMITTED;

    /**
     * List all applications.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(): Response
    {
        return $this->send(new ListApplications);
    }

    /**
     * Create a new application.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(
        string $name,
        string $repository,
        string $region,
        SourceControlProviderType $sourceControlProviderType = SourceControlProviderType::GitHub,
        ?string $rootDirectory = null,
        ?string $clusterId = null,
    ): Response {
        return $this->send(new CreateApplication(
            name: $name,
            repository: $repository,
            region: $region,
            sourceControlProviderType: $sourceControlProviderType,
            rootDirectory: $rootDirectory,
            clusterId: $clusterId,
        ));
    }

    /**
     * Get a specific application by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $applicationId): Response
    {
        return $this->send(new GetApplication(applicationId: $applicationId));
    }

    /**
     * Update an existing application.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(
        string $applicationId,
        ?string $name = self::OMITTED,
        ?string $slug = self::OMITTED,
        ?string $defaultEnvironmentId = self::OMITTED,
        ?string $repository = self::OMITTED,
        ?string $slackChannel = self::OMITTED,
        ?SourceControlProviderType $sourceControlProviderType = null,
    ): Response {
        return $this->send(new UpdateApplication(
            applicationId: $applicationId,
            name: $name,
            slug: $slug,
            defaultEnvironmentId: $defaultEnvironmentId,
            repository: $repository,
            slackChannel: $slackChannel,
            sourceControlProviderType: $sourceControlProviderType,
        ));
    }

    /**
     * Delete an application.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $applicationId): Response
    {
        return $this->send(new DeleteApplication(applicationId: $applicationId));
    }
}
