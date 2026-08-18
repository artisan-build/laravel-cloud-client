<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Commands;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get the output of a command.
 */
final class GetCommandOutput extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $commandId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('commands', $this->commandId, 'output');
    }
}
