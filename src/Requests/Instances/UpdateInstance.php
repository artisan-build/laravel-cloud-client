<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing instance.
 */
final class UpdateInstance extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $instanceId,
        protected ?InstanceSize $size = null,
        // `uses_scheduler` is how Laravel Cloud turns the scheduler on for an
        // instance; it is a flag on the instance, not a resource of its own.
        // Null leaves the current setting alone, which is why it is not folded
        // into the array_filter below as a plain boolean.
        protected ?bool $usesScheduler = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('instances', $this->instanceId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'size' => $this->size?->value,
            'uses_scheduler' => $this->usesScheduler,
        ], fn ($value) => $value !== null);
    }
}
