<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific instance by ID.
 */
final class GetInstance extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $instanceId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('instances', $this->instanceId);
    }
}
