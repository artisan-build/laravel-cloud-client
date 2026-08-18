<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Deployments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific deployment by ID.
 */
final class GetDeployment extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $deploymentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('deployments', $this->deploymentId);
    }
}
