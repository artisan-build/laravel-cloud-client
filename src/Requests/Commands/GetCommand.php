<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Commands;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get a specific command by ID.
 */
final class GetCommand extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $commandId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('commands', $this->commandId);
    }
}
