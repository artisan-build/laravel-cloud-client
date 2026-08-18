<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\CacheSize;
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
        string $type,
        string $name,
        string $region,
        CacheSize $size,
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
    public function update(string $cacheId, ?string $name = null, ?CacheSize $size = null): Response
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
