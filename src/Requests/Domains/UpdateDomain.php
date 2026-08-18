<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Requests\Domains;

use ArtisanBuild\LaravelCloudClient\Enums\DomainVerificationMethod;
use ArtisanBuild\LaravelCloudClient\Support\Path;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Update an existing domain.
 */
final class UpdateDomain extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $domainId,
        protected DomainVerificationMethod $verificationMethod,
    ) {}

    public function resolveEndpoint(): string
    {
        return Path::make('domains', $this->domainId);
    }

    /**
     * @return array{verification_method: string}
     */
    protected function defaultBody(): array
    {
        return [
            'verification_method' => $this->verificationMethod->value,
        ];
    }
}
