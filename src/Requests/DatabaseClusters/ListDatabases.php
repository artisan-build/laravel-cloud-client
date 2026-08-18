<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all databases in a cluster.
 */
final class ListDatabases extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $clusterId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters', $this->clusterId, 'databases');
    }
}
