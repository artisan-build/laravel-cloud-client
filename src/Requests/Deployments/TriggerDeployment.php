<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Deployments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Trigger a new deployment for an environment.
 */
final class TriggerDeployment extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected string $environmentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'deployments');
    }
}
