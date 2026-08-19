<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CacheType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use ArtisanBuild\LaravelCloudClient\Support\Value;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new cache (Redis instance).
 */
final class CreateCache extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected CacheType|string $type,
        protected string $name,
        protected string $region,
        protected CacheSize|string $size,
        protected bool $autoUpgradeEnabled,
        protected bool $isPublic,
        protected ?string $clusterId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('caches');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'type' => Value::of($this->type),
            'name' => $this->name,
            'region' => $this->region,
            'size' => Value::of($this->size),
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
            'is_public' => $this->isPublic,
            'cluster_id' => $this->clusterId,
        ], fn ($value) => $value !== null);
    }
}
