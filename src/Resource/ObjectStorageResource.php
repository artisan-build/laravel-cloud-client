<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\CreateBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\CreateBucketAccessKey;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\DeleteBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\DeleteBucketAccessKey;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\GetBucket;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\ListBucketAccessKeys;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\ListBuckets;
use ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage\UpdateBucket;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing object storage.
 */
final class ObjectStorageResource extends Resource
{
    /**
     * List all buckets for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function listBuckets(): Response
    {
        return $this->send(new ListBuckets);
    }

    /**
     * Create a new bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function createBucket(
        string $name,
        string $visibility,
        string $jurisdiction,
        string $keyName,
        string $keyPermission,
    ): Response {
        return $this->send(new CreateBucket(
            name: $name,
            visibility: $visibility,
            jurisdiction: $jurisdiction,
            keyName: $keyName,
            keyPermission: $keyPermission,
        ));
    }

    /**
     * Get a specific bucket by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function getBucket(string $bucketId): Response
    {
        return $this->send(new GetBucket(bucketId: $bucketId));
    }

    /**
     * Update an existing bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function updateBucket(string $bucketId, ?string $name = null, ?bool $public = null): Response
    {
        return $this->send(new UpdateBucket(
            bucketId: $bucketId,
            name: $name,
            public: $public,
        ));
    }

    /**
     * Delete a bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteBucket(string $bucketId): Response
    {
        return $this->send(new DeleteBucket(bucketId: $bucketId));
    }

    /**
     * List all access keys for a bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function listAccessKeys(string $bucketId): Response
    {
        return $this->send(new ListBucketAccessKeys(bucketId: $bucketId));
    }

    /**
     * Create a new access key for a bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function createAccessKey(string $bucketId, string $name, string $permission): Response
    {
        return $this->send(new CreateBucketAccessKey(
            bucketId: $bucketId,
            name: $name,
            permission: $permission,
        ));
    }

    /**
     * Delete an access key from a bucket.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function deleteAccessKey(string $bucketId, string $accessKeyId): Response
    {
        return $this->send(new DeleteBucketAccessKey(
            bucketId: $bucketId,
            accessKeyId: $accessKeyId,
        ));
    }
}
