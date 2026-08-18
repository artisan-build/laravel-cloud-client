<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\BackgroundProcesses;

use ArtisanBuild\LaravelCloudClient\Enums\DaemonType;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new background process.
 */
final class CreateBackgroundProcess extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $instanceId,
        protected DaemonType $type,
        protected string $command,
        protected int $processes,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('instances', $this->instanceId, 'background-processes');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'type' => $this->type->value,
            'command' => $this->command,
            'processes' => $this->processes,
        ];
    }
}
