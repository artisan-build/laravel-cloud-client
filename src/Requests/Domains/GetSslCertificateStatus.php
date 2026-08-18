<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Domains;

use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get the SSL certificate status for a domain.
 */
final class GetSslCertificateStatus extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $domainId,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('domains', $this->domainId, 'ssl');
    }
}
