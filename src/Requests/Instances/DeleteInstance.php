<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete an instance (scale down).
 */
final class DeleteInstance extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $instanceId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('instances', $this->instanceId);
    }
}
