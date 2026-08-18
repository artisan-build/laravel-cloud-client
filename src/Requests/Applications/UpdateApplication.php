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
 * Update an existing application.
 */
final class UpdateApplication extends Request implements HasBody
{
    use HasJsonBody;

    public const OMITTED = '__laravel_cloud_omitted__';

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $applicationId,
        protected ?string $name = self::OMITTED,
        protected ?string $slug = self::OMITTED,
        protected ?string $defaultEnvironmentId = self::OMITTED,
        protected ?string $repository = self::OMITTED,
        protected ?string $slackChannel = self::OMITTED,
        protected ?SourceControlProviderType $sourceControlProviderType = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications', $this->applicationId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [];

        foreach ([
            'name' => $this->name,
            'slug' => $this->slug,
            'default_environment_id' => $this->defaultEnvironmentId,
            'repository' => $this->repository,
            'slack_channel' => $this->slackChannel,
        ] as $key => $value) {
            if ($value !== self::OMITTED) {
                $body[$key] = $value;
            }
        }

        if ($this->sourceControlProviderType !== null) {
            $body['source_control_provider_type'] = $this->sourceControlProviderType->value;
        }

        return $body;
    }
}
