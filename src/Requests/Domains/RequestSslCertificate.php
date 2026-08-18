<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Domains;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Request an SSL certificate for a domain.
 */
final class RequestSslCertificate extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected string $domainId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('domains', $this->domainId, 'verify');
    }
}
