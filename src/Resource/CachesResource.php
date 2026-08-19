<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
use ArtisanBuild\LaravelCloudClient\Enums\CacheType;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\CreateCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\DeleteCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\FlushCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\GetCache;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\ListCaches;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\ListCacheTypes;
use ArtisanBuild\LaravelCloudClient\Requests\Caches\UpdateCache;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing caches.
 */
final class CachesResource extends Resource
{
    /**
     * List all caches for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(): Response
    {
        return $this->send(new ListCaches);
    }

    /**
     * Create a new cache (Redis instance).
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function create(
        CacheType|string $type,
        string $name,
        string $region,
        CacheSize|string $size,
        bool $autoUpgradeEnabled,
        bool $isPublic,
        ?string $clusterId = null,
    ): Response {
        return $this->send(new CreateCache(
            type: $type,
            name: $name,
            region: $region,
            size: $size,
            autoUpgradeEnabled: $autoUpgradeEnabled,
            isPublic: $isPublic,
            clusterId: $clusterId,
        ));
    }

    /**
     * List the cache types available when creating a cache, each with the
     * sizes and regions it supports.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function types(): Response
    {
        return $this->send(new ListCacheTypes);
    }

    /**
     * Get a specific cache by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $cacheId): Response
    {
        return $this->send(new GetCache(cacheId: $cacheId));
    }

    /**
     * Update an existing cache.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(string $cacheId, ?string $name = null, CacheSize|string|null $size = null): Response
    {
        return $this->send(new UpdateCache(
            cacheId: $cacheId,
            name: $name,
            size: $size,
        ));
    }

    /**
     * Delete a cache.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $cacheId): Response
    {
        return $this->send(new DeleteCache(cacheId: $cacheId));
    }

    /**
     * Flush (clear) a cache.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * The published purge edge cache operation is environment-level and is not a cache-resource flush operation.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function flush(string $cacheId): Response
    {
        return $this->send(new FlushCache(cacheId: $cacheId));
    }
}
