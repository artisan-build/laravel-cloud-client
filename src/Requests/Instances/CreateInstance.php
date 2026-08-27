<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Enums\InstanceScalingType;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceSize;
use ArtisanBuild\LaravelCloudClient\Enums\InstanceType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use ArtisanBuild\LaravelCloudClient\Support\Value;
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
        protected InstanceSize|string $size,
        protected InstanceScalingType $scalingType,
        // NULL OMITS IT, and omission is the only correct body for two of the
        // three scaling types. Cloud's schema: "Only applicable to the `custom`
        // scaling type, and rejected when used with `auto`. Not applicable to
        // managed queues, which always scale to zero when idle."
        //
        // Nullable rather than defaulted-to-null because it sits before two
        // parameters that have no default; and null means ABSENT rather than
        // `null` on the wire, unlike the two timeouts below, which the schema
        // types `["integer","null"]` and which are correct as nulls.
        protected ?int $minReplicas,
        protected ?int $visibilityTimeout,
        protected ?int $shutdownTimeout,
        protected ?int $maxReplicas = null,
        // See UpdateInstance: the scheduler is a flag on the instance.
        protected ?bool $usesScheduler = null,
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
            'size' => Value::of($this->size),
            'scaling_type' => $this->scalingType->value,
        ];

        // Omitted rather than appended at the end, so the body still reads in
        // the schema's own order and a service instance's fixture does not have
        // to be reshuffled to say the same thing.
        if ($this->minReplicas !== null) {
            $body['min_replicas'] = $this->minReplicas;
        }

        // Sent even as nulls: the schema types both `["integer","null"]`, so a
        // null is a value here and not an absence.
        $body['visibility_timeout'] = $this->visibilityTimeout;
        $body['shutdown_timeout'] = $this->shutdownTimeout;

        if ($this->maxReplicas !== null) {
            $body['max_replicas'] = $this->maxReplicas;
        }

        if ($this->usesScheduler !== null) {
            $body['uses_scheduler'] = $this->usesScheduler;
        }

        return $body;
    }
}
