<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new instance (scale up).
 */
final class CreateInstance extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $environmentId,
        protected string $name,
        protected InstanceType $type,
        protected InstanceSize $size,
        protected InstanceScalingType $scalingType,
        protected int $minReplicas,
        protected ?int $visibilityTimeout,
        protected ?int $shutdownTimeout,
        protected ?int $maxReplicas = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'instances');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [
            'name' => $this->name,
            'type' => $this->type->value,
            'size' => $this->size->value,
            'scaling_type' => $this->scalingType->value,
            'min_replicas' => $this->minReplicas,
            'visibility_timeout' => $this->visibilityTimeout,
            'shutdown_timeout' => $this->shutdownTimeout,
        ];

        if ($this->maxReplicas !== null) {
            $body['max_replicas'] = $this->maxReplicas;
        }

        return $body;
    }
}
