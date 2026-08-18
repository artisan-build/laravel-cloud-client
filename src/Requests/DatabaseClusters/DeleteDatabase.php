<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete a database from a cluster.
 */
final class DeleteDatabase extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $clusterId,
        protected string $databaseName,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters', $this->clusterId, 'databases', $this->databaseName);
    }
}
