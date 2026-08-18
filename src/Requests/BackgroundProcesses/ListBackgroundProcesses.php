<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all background processes for an environment.
 */
final class ListBackgroundProcesses extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $instanceId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('instances', $this->instanceId, 'background-processes');
    }
}
