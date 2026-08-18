<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\ObjectStorage;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create a new access key for a bucket.
 */
final class CreateBucketAccessKey extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $bucketId,
        protected string $name,
        protected string $permission,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('buckets', $this->bucketId, 'keys');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
            'permission' => $this->permission,
        ];
    }
}
