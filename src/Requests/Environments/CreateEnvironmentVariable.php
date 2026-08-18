<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Enums\EnvironmentVariablesInsertMethod;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new environment variable.
 */
final class CreateEnvironmentVariable extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $environmentId,
        protected EnvironmentVariablesInsertMethod $insertMethod,
        /** @var non-empty-list<array{key: string, value: string}> */
        protected array $variables,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'variables');
    }

    /**
     * @return array{method: string, variables: non-empty-list<array{key: string, value: string}>}
     */
    protected function defaultBody(): array
    {
        return [
            'method' => $this->insertMethod->value,
            'variables' => $this->variables,
        ];
    }
}
