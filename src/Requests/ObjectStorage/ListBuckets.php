<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all object storage buckets.
 */
final class ListBuckets extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('buckets');
    }
}
