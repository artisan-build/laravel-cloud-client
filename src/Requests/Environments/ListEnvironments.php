<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all environments for an application.
 */
final class ListEnvironments extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $applicationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications', $this->applicationId, 'environments');
    }
}
