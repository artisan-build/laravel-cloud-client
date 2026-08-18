<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new database in a cluster.
 */
final class CreateDatabase extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $clusterId,
        protected string $name,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters', $this->clusterId, 'databases');
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
