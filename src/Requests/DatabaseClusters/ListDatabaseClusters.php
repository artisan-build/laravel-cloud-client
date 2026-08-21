<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\DatabaseClusters;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List all database clusters.
 *
 * Like every other listing, this returns `"relationships": []` unless an
 * include asks for more — so a caller that needs to know which schemas belong
 * to which cluster has to say so. The only name Cloud accepts here is
 * `databases`; `schemas` was the old spelling and is now refused outright with
 * a 400 that names the replacement.
 */
final class ListDatabaseClusters extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        /** A Cloud include name — `databases` is the only one this endpoint allows. */
        protected ?string $include = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('databases', 'clusters');
    }

    /**
     * @return array<string, string>
     */
    protected function defaultQuery(): array
    {
        return $this->include === null ? [] : ['include' => $this->include];
    }
}
