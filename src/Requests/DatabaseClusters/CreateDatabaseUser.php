<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SensitiveParameter;

/**
 * Create a new user in a database cluster.
 */
final class CreateDatabaseUser extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $clusterId,
        protected string $username,
        #[SensitiveParameter] protected ?string $password = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('database-clusters', $this->clusterId, 'users');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'username' => $this->username,
            'password' => $this->password,
        ], fn ($value) => $value !== null);
    }
}
