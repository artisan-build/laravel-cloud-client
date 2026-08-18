<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing bucket.
 */
final class UpdateBucket extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $bucketId,
        protected ?string $name = null,
        protected ?bool $public = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('buckets', $this->bucketId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'public' => $this->public,
        ], fn ($value) => $value !== null);
    }
}
