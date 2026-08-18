<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Applications;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all applications.
 */
final class ListApplications extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('applications');
    }
}
