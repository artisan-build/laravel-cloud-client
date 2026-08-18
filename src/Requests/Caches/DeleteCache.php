<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete a cache.
 */
final class DeleteCache extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $cacheId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('caches', $this->cacheId);
    }
}
