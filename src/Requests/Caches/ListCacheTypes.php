<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List the cache types available when creating a cache.
 *
 * Each entry carries the sizes and regions that type supports, which makes
 * this the authority on a pairing the schema does not otherwise express: the
 * bundled CacheType::supports() infers it from size prefixes, and this does
 * not have to infer anything.
 */
final class ListCacheTypes extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('caches', 'types');
    }
}
