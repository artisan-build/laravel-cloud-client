<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific background process by ID.
 */
final class GetBackgroundProcess extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $processId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('background-processes', $this->processId);
    }
}
