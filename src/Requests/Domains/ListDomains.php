<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Domains;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all domains for an environment.
 */
final class ListDomains extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $environmentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'domains');
    }
}
