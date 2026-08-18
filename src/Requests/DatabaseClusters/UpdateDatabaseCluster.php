<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing database cluster.
 */
final class UpdateDatabaseCluster extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $clusterId,
        /** @var array<string, mixed> */
        protected array $databaseConfig,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters', $this->clusterId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'config' => $this->databaseConfig,
        ];
    }
}
