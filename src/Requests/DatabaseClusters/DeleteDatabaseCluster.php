<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete a database cluster.
 */
final class DeleteDatabaseCluster extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $clusterId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters', $this->clusterId);
    }
}
