<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete an access key from a bucket.
 */
final class DeleteBucketAccessKey extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $bucketId,
        protected string $accessKeyId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('bucket-keys', $this->accessKeyId);
    }
}
