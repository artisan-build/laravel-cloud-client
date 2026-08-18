<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing cache.
 */
final class UpdateCache extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $cacheId,
        protected ?string $name = null,
        protected ?CacheSize $size = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('caches', $this->cacheId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'size' => $this->size?->value,
        ], fn ($value) => $value !== null);
    }
}
