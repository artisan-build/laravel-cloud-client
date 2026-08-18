<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Enums\DatabaseType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new database cluster.
 */
final class CreateDatabaseCluster extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected DatabaseType $type,
        protected string $name,
        protected string $region,
        /** @var array<string, mixed> */
        protected array $databaseConfig,
        protected ?string $clusterId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'type' => $this->type->value,
            'name' => $this->name,
            'region' => $this->region,
            'config' => $this->databaseConfig,
            'cluster_id' => $this->clusterId,
        ], fn ($value) => $value !== null);
    }
}
