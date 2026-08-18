<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Commands;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Execute an Artisan command on an environment.
 */
final class ExecuteCommand extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $environmentId,
        protected string $command,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'commands');
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return [
            'command' => $this->command,
        ];
    }
}
