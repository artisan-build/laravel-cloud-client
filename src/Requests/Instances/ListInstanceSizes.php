<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Instances;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List the available instance sizes, grouped by category.
 *
 * The response separates `general` from `managed_queue`, which is the API's
 * own statement of which sizes are queue sizes — better evidence than the
 * `mq` prefix the bundled enum has to infer it from.
 */
final class ListInstanceSizes extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return Path::make('instances', 'sizes');
    }
}
