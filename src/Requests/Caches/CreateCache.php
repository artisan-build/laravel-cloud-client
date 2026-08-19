<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CacheType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
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
        protected CacheType $type,
        protected string $name,
        protected string $region,
        protected CacheSize $size,
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
            'type' => $this->type->value,
            'name' => $this->name,
            'region' => $this->region,
            'size' => $this->size->value,
            'auto_upgrade_enabled' => $this->autoUpgradeEnabled,
            'is_public' => $this->isPublic,
            'cluster_id' => $this->clusterId,
        ], fn ($value) => $value !== null);
    }
}
