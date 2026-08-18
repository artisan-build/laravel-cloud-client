<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Caches;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all caches.
 */
final class ListCaches extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('caches');
    }
}
