<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Applications;

use ArtisanBuild\LaravelCloudClient\Enums\SourceControlProviderType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new application.
 */
final class CreateApplication extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $name,
        protected string $repository,
        protected string $region,
        protected SourceControlProviderType $sourceControlProviderType,
        protected ?string $rootDirectory = null,
        protected ?string $clusterId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'repository' => $this->repository,
            'region' => $this->region,
            'source_control_provider_type' => $this->sourceControlProviderType->value,
            'root_directory' => $this->rootDirectory,
            'cluster_id' => $this->clusterId,
        ], fn ($value) => $value !== null);
    }
}
