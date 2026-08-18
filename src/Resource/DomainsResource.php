<?php

declare(strict_types=1);

namespace ArtisanBuild\LaravelCloudClient\Resource;

use ArtisanBuild\LaravelCloudClient\Enums\DomainVerificationMethod;
use ArtisanBuild\LaravelCloudClient\Exceptions\ApiException;
use ArtisanBuild\LaravelCloudClient\Exceptions\AuthenticationException;
use ArtisanBuild\LaravelCloudClient\Exceptions\NotFoundException;
use ArtisanBuild\LaravelCloudClient\Exceptions\RateLimitException;
use ArtisanBuild\LaravelCloudClient\Exceptions\ValidationException;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\AddDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\DeleteDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\GetDomain;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\GetSslCertificateStatus;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\ListDomains;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\RequestSslCertificate;
use ArtisanBuild\LaravelCloudClient\Requests\Domains\UpdateDomain;
use ArtisanBuild\LaravelCloudClient\Resource;
use Saloon\Http\Response;

/**
 * Resource for managing domains.
 */
final class DomainsResource extends Resource
{
    /**
     * List all domains for an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function list(string $environmentId): Response
    {
        return $this->send(new ListDomains(environmentId: $environmentId));
    }

    /**
     * Add a new domain to an environment.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function add(string $environmentId, string $domain): Response
    {
        return $this->send(new AddDomain(
            environmentId: $environmentId,
            domain: $domain,
        ));
    }

    /**
     * Get a specific domain by ID.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function get(string $domainId): Response
    {
        return $this->send(new GetDomain(domainId: $domainId));
    }

    /**
     * Update an existing domain.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function update(string $domainId, DomainVerificationMethod $verificationMethod): Response
    {
        return $this->send(new UpdateDomain(
            domainId: $domainId,
            verificationMethod: $verificationMethod,
        ));
    }

    /**
     * Delete a domain.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function delete(string $domainId): Response
    {
        return $this->send(new DeleteDomain(domainId: $domainId));
    }

    /**
     * Request an SSL certificate for a domain.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function requestSsl(string $domainId): Response
    {
        return $this->send(new RequestSslCertificate(domainId: $domainId));
    }

    /**
     * Get the SSL certificate status for a domain.
     *
     * No published Laravel Cloud operation corresponds to this call; it is unverified against the API and must not be relied upon.
     * Published domain operations manage domains themselves and do not include a dedicated SSL-certificate-status operation.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function getSslStatus(string $domainId): Response
    {
        return $this->send(new GetSslCertificateStatus(domainId: $domainId));
    }
}
