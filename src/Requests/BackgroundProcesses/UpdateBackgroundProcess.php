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
 * Update an existing background process.
 */
final class UpdateBackgroundProcess extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $processId,
        protected ?DaemonType $type = null,
        protected ?string $command = null,
        protected ?int $processes = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('background-processes', $this->processId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'type' => $this->type?->value,
            'command' => $this->command,
            'processes' => $this->processes,
        ], fn ($value) => $value !== null);
    }
}
