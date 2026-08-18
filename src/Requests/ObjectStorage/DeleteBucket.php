<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete a bucket.
 */
final class DeleteBucket extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $bucketId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('buckets', $this->bucketId);
    }
}
