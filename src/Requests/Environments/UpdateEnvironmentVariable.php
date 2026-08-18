<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing environment variable.
 */
final class UpdateEnvironmentVariable extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected string $environmentId,
        protected string $key,
        protected string $value,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'variables', $this->key);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return [
            'value' => $this->value,
        ];
    }
}
