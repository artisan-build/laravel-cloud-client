<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new object storage bucket.
 */
final class CreateBucket extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $name,
        protected string $visibility,
        protected string $jurisdiction,
        protected string $keyName,
        protected string $keyPermission,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('buckets');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
            'visibility' => $this->visibility,
            'jurisdiction' => $this->jurisdiction,
            'key_name' => $this->keyName,
            'key_permission' => $this->keyPermission,
        ];
    }
}
