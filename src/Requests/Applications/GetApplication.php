<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Applications;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific application by ID.
 */
final class GetApplication extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $applicationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('applications', $this->applicationId);
    }
}
