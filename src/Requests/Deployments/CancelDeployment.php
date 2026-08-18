<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Deployments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Cancel a running deployment.
 */
final class CancelDeployment extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $deploymentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('deployments', $this->deploymentId);
    }
}
