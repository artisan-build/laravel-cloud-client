<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Meta;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List available regions.
 */
final class ListRegions extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('meta', 'regions');
    }
}
