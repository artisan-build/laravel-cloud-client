<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Environments;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Delete an environment variable.
 */
final class DeleteEnvironmentVariable extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $environmentId,
        /** @var non-empty-list<string> */
        protected array $keys,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('environments', $this->environmentId, 'variables', 'delete');
    }

    /**
     * @return array{keys: non-empty-list<string>}
     */
    protected function defaultBody(): array
    {
        return [
            'keys' => $this->keys,
        ];
    }
}
