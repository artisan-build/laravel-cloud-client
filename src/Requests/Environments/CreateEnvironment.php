<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new environment for an application.
 */
final class CreateEnvironment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $applicationId,
        protected string $name,
        protected string $branch,
        protected ?string $clusterId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications', $this->applicationId, 'environments');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'branch' => $this->branch,
            'cluster_id' => $this->clusterId,
        ], fn ($value) => $value !== null);
    }
}
