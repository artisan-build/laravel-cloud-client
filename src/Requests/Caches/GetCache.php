<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific cache by ID.
 */
final class GetCache extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $cacheId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('caches', $this->cacheId);
    }
}
