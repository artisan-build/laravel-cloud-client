<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Applications;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete an application.
 */
final class DeleteApplication extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $applicationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications', $this->applicationId);
    }
}
