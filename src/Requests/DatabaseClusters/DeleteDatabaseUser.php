<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete a user from a database cluster.
 */
final class DeleteDatabaseUser extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $clusterId,
        protected string $username,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('database-clusters', $this->clusterId, 'users', $this->username);
    }
}
