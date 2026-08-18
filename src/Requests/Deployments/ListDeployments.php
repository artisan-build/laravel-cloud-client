<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Deployments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all deployments for an environment.
 */
final class ListDeployments extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $environmentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'deployments');
    }
}
